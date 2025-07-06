import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split, StratifiedKFold
from sklearn.metrics import (
    confusion_matrix, accuracy_score, precision_score,
    recall_score, f1_score, roc_auc_score, roc_curve
)
import joblib
from io import BytesIO  # Untuk menyimpan Excel ke memori

# Fungsi menghitung rata-rata, standar deviasi, dan prior
def summarize_by_class(X, y):
    summaries = {}
    if not isinstance(X, pd.DataFrame):
        pass
    for class_value in np.unique(y):
        class_data = X[y == class_value]
        summaries[class_value] = {
            'mean': class_data.mean(),
            'std': class_data.std(),
            'prior': len(class_data) / len(y)
        }
    return summaries

# Fungsi likelihood
def calculate_probability(x, mean, std):
    std = 1e-6 if std == 0 else std
    exponent = np.exp(-((x - mean) ** 2) / (2 * std ** 2))
    return (1 / (np.sqrt(2 * np.pi) * std)) * exponent

# Posterior
def calculate_class_probabilities(summaries, input_data):
    probabilities = {}
    for class_value, stats in summaries.items():
        prob = stats['prior']
        for i in range(len(input_data)):
            mean = stats['mean'].values[i]
            std = stats['std'].values[i]
            prob *= calculate_probability(input_data[i], mean, std)
        probabilities[class_value] = prob
    return probabilities

def predict(summaries, input_data):
    probabilities = calculate_class_probabilities(summaries, input_data)
    return max(probabilities, key=probabilities.get)

def predict_proba(summaries, input_data, positive_class_label=1):
    class_likelihood_times_priors = calculate_class_probabilities(summaries, input_data)
    prob_positive_numerator = class_likelihood_times_priors.get(positive_class_label, 0.0)
    evidence_px = sum(class_likelihood_times_priors.values())
    if evidence_px == 0:
        num_classes = len(summaries)
        return 1.0 / num_classes if num_classes > 0 else 0.0 
    posterior_prob_positive = prob_positive_numerator / evidence_px
    return posterior_prob_positive

# ==================== STREAMLIT APP ====================
st.title("🩺 Prediksi Diabetes Tipe 2")
st.subheader("Dengan Naive Bayes Manual")

st.sidebar.header("📤 Upload Dataset")
uploaded_file = st.sidebar.file_uploader("Pilih file CSV", type="csv")

if uploaded_file:
    data = pd.read_csv(uploaded_file)
    st.write("### Data Awal")
    st.dataframe(data.head())

    required_columns = ['gdp', 'tekanan_darah', 'imt', 'umur', 'hasil']
    if not all(col in data.columns for col in required_columns):
        st.error(f"Dataset harus memiliki kolom: {', '.join(required_columns)}")
    else:
        X = data[['gdp', 'tekanan_darah', 'imt', 'umur']]
        y = data['hasil'] 

        st.write("### Distribusi Kelas")
        st.bar_chart(y.value_counts())

        st.write("### Korelasi Fitur")
        fig_corr, ax_corr = plt.subplots()
        sns.heatmap(X.corr(), annot=True, cmap='coolwarm', ax=ax_corr)
        st.pyplot(fig_corr)

        st.write("### Distribusi Fitur per Kelas Diabetes")
        for fitur in X.columns:
            fig_box, ax_box = plt.subplots(figsize=(7, 5))
            sns.boxplot(x=y, y=data[fitur], ax=ax_box, palette=['#4c72b0', '#dd8452'])
            ax_box.set_xticklabels(['Tidak Diabetes (0)', 'Diabetes (1)'])
            ax_box.set_title(f'Distribusi {fitur.capitalize()} berdasarkan Kelas Diabetes', fontsize=14)
            ax_box.set_xlabel('Kelas Diabetes', fontsize=12)
            ax_box.set_ylabel(f'{fitur.capitalize()}', fontsize=12)
            ax_box.grid(True, linestyle='--', alpha=0.7)
            st.pyplot(fig_box)

        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)

        st.write("---")
        st.write("### 💾 Unduh Data Hasil Pembagian (Split)")

        # Ekspor Excel Training
        training_data_to_download = pd.concat([X_train, y_train], axis=1)
        excel_training = BytesIO()
        training_data_to_download.to_excel(excel_training, index=False, engine='openpyxl')
        st.download_button(
            label="📥 Unduh Data Training (80%)",
            data=excel_training.getvalue(),
            file_name='data_training_diabetes.xlsx',
            mime='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        )

        # Ekspor Excel Testing
        testing_data_to_download = pd.concat([X_test, y_test], axis=1)
        excel_testing = BytesIO()
        testing_data_to_download.to_excel(excel_testing, index=False, engine='openpyxl')
        st.download_button(
            label="📥 Unduh Data Testing (20%)",
            data=excel_testing.getvalue(),
            file_name='data_testing_diabetes.xlsx',
            mime='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        )

        st.write("---")

        summaries = summarize_by_class(X_train, y_train)

        st.write("### Statistik Mean & Std per Kelas (dari Data Training)")
        for label, stats in summaries.items():
            st.write(f"**Kelas {label}**")
            st.write("Mean:", stats['mean'])
            st.write("Std:", stats['std'])

        model_filename = 'naive_bayes_modelGG.pkl'
        joblib.dump({'summaries': summaries, 'feature_columns': X.columns.tolist()}, model_filename)
        st.success(f"Model berhasil disimpan sebagai `{model_filename}`")

        y_pred = [predict(summaries, row.values) for _, row in X_test.iterrows()]
        y_pred_proba_test = [predict_proba(summaries, row.values, positive_class_label=1) for _, row in X_test.iterrows()]

        st.write("### Confusion Matrix (pada Data Testing)")
        conf_matrix = confusion_matrix(y_test, y_pred)
        fig_cm, ax_cm = plt.subplots()
        sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues',
                    xticklabels=['Tidak Diabetes', 'Diabetes'],
                    yticklabels=['Tidak Diabetes', 'Diabetes'], ax=ax_cm)
        ax_cm.set_xlabel("Prediksi")
        ax_cm.set_ylabel("Aktual")
        st.pyplot(fig_cm)

        st.write("### Evaluasi Model (pada Data Testing)")
        accuracy_val = accuracy_score(y_test, y_pred)
        precision_val = precision_score(y_test, y_pred, zero_division=0)
        recall_val = recall_score(y_test, y_pred, zero_division=0)
        f1_val = f1_score(y_test, y_pred, zero_division=0)
        try:
            auc_val_test = roc_auc_score(y_test, y_pred_proba_test)
        except ValueError:
            auc_val_test = 0.0
            st.warning("AUC tidak dapat dihitung karena data test hanya memiliki satu kelas.")

        st.write(f"- Akurasi: **{accuracy_val:.2f}**")
        st.write(f"- Precision: **{precision_val:.2f}**")
        st.write(f"- Recall: **{recall_val:.2f}**")
        st.write(f"- F1 Score: **{f1_val:.2f}**")
        st.write(f"- AUC: **{auc_val_test:.2f}**")

        st.write("### ROC Curve (pada Data Testing)")
        fpr, tpr, thresholds = roc_curve(y_test, y_pred_proba_test)
        fig_roc, ax_roc = plt.subplots()
        ax_roc.plot(fpr, tpr, color='blue', lw=2, label=f'ROC curve (AUC = {auc_val_test:.2f})')
        ax_roc.plot([0, 1], [0, 1], color='red', lw=2, linestyle='--', label='Garis Acak (AUC = 0.50)')
        ax_roc.set_xlim([0.0, 1.0])
        ax_roc.set_ylim([0.0, 1.05])
        ax_roc.set_xlabel('False Positive Rate (FPR)')
        ax_roc.set_ylabel('True Positive Rate (TPR)')
        ax_roc.set_title('Receiver Operating Characteristic (ROC) Curve')
        ax_roc.legend(loc="lower right")
        ax_roc.grid(alpha=0.3)
        st.pyplot(fig_roc)

        st.write("### K-Fold Cross Validation (5 Fold)")
        kfold = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
        kfold_metrics = []

        for fold, (train_idx, test_idx) in enumerate(kfold.split(X, y), 1):
            X_tr, X_te = X.iloc[train_idx], X.iloc[test_idx]
            y_tr, y_te = y.iloc[train_idx], y.iloc[test_idx]
            
            summ_kf = summarize_by_class(X_tr, y_tr)
            y_pred_kf_labels = [predict(summ_kf, row.values) for _, row in X_te.iterrows()]
            y_pred_kf_proba = [predict_proba(summ_kf, row.values, positive_class_label=1) for _, row in X_te.iterrows()]
            
            acc_kf = accuracy_score(y_te, y_pred_kf_labels) * 100
            prec_kf = precision_score(y_te, y_pred_kf_labels, zero_division=0) * 100
            rec_kf = recall_score(y_te, y_pred_kf_labels, zero_division=0) * 100
            f1_kf = f1_score(y_te, y_pred_kf_labels, zero_division=0) * 100
            try:
                auc_kf = roc_auc_score(y_te, y_pred_kf_proba) * 100
            except ValueError:
                auc_kf = 0.0

            kfold_metrics.append({
                "Fold": fold,
                "Akurasi (%)": acc_kf,
                "Precision (%)": prec_kf,
                "Recall (%)": rec_kf,
                "F1-Score (%)": f1_kf,
                "AUC (%)": auc_kf
            })

        df_kfold = pd.DataFrame(kfold_metrics)
        st.dataframe(df_kfold.style.format("{:.2f}"))
        st.write("#### Rata-rata K-Fold")
        st.write(f"- Akurasi: **{df_kfold['Akurasi (%)'].mean():.2f}%**")
        st.write(f"- Precision: **{df_kfold['Precision (%)'].mean():.2f}%**")
        st.write(f"- Recall: **{df_kfold['Recall (%)'].mean():.2f}%**")
        st.write(f"- F1 Score: **{df_kfold['F1-Score (%)'].mean():.2f}%**")
        st.write(f"- AUC: **{df_kfold['AUC (%)'].mean():.2f}%**")

        # ========= Prediksi Data Baru =========
        st.write("---")
        st.write("### Prediksi Data Baru")
        feature_order = X.columns.tolist()

        form_key_prefix = "pred_input_" 
        with st.form(key="prediction_form"):
            col1, col2 = st.columns(2)
            with col1:
                gdp_val = st.number_input(f"{feature_order[0].replace('_',' ').capitalize()} (mg/dL)", min_value=0, max_value=400, value=100, key=form_key_prefix + feature_order[0])
                tekanan_val = st.number_input(f"{feature_order[1].replace('_',' ').capitalize()} (mmHg)", min_value=0, max_value=250, value=120, key=form_key_prefix + feature_order[1])
            with col2:
                imt_val = st.number_input(f"{feature_order[2].upper()} (kg/m²)", min_value=10.0, max_value=70.0, value=25.0, step=0.1, format="%.1f", key=form_key_prefix + feature_order[2])
                umur_val = st.number_input(f"{feature_order[3].capitalize()} (tahun)", min_value=1, max_value=120, value=35, key=form_key_prefix + feature_order[3])
            
            input_map = {feature_order[0]: gdp_val, feature_order[1]: tekanan_val, feature_order[2]: imt_val, feature_order[3]: umur_val}
            input_data_new = [input_map[feature] for feature in feature_order]
            submitted = st.form_submit_button("Prediksi")

        if submitted:
            try:
                current_summaries = summaries 
                hasil_label = predict(current_summaries, input_data_new)
                prob_result = predict_proba(current_summaries, input_data_new, positive_class_label=1)

                if hasil_label == 1:
                    st.error(
                        f"Hasil: **Berisiko Diabetes Tipe 2** ⚠️\n\n"
                        f"Probabilitas: **{prob_result:.2%}**"
                    )
                else:
                    st.success(
                        f"Hasil: **Tidak Berisiko Diabetes Tipe 2** 👍\n\n"
                        f"Probabilitas: **{prob_result:.2%}**"
                    )
            except Exception as e:
                st.error(f"Terjadi kesalahan saat prediksi: {e}")
                st.error("Pastikan model sudah dilatih dan file model ada.")
else:
    st.info("Silakan upload dataset CSV terlebih dahulu untuk memulai.")
