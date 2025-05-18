import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split, StratifiedKFold
from sklearn.metrics import (
    confusion_matrix, accuracy_score, precision_score,
    recall_score, f1_score
)
import joblib

# ==================== FUNGSI NAIVE BAYES ====================
def calculate_probability(x, mean, std):
    std = 1e-6 if std == 0 else std
    exponent = np.exp(-((x - mean) ** 2) / (2 * std ** 2))
    return (1 / (np.sqrt(2 * np.pi) * std)) * exponent

def summarize_by_class(X, y):
    summaries = {}
    for class_value in np.unique(y):
        class_data = X[y == class_value]
        summaries[class_value] = {
            'mean': class_data.mean(),
            'std': class_data.std(),
            'prior': len(class_data) / len(y)
        }
    return summaries

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

# ==================== STREAMLIT APP ====================
st.title("🩺 Prediksi Diabetes Tipe 2")
st.subheader("Dengan Naive Bayes Manual")

st.sidebar.header("📤 Upload Dataset")
uploaded_file = st.sidebar.file_uploader("Pilih file CSV", type="csv")

if uploaded_file:
    data = pd.read_csv(uploaded_file)
    st.write("### Data Awal")
    st.dataframe(data.head())

    X = data[['gdp', 'tekanan_darah', 'imt', 'umur']]
    y = data['hasil']

    st.write("### Distribusi Kelas")
    st.bar_chart(y.value_counts())

    st.write("### Korelasi Fitur")
    fig, ax = plt.subplots()
    sns.heatmap(X.corr(), annot=True, cmap='coolwarm', ax=ax)
    st.pyplot(fig)

    st.write("### Distribusi Fitur per Kelas Diabetes")

    for fitur in X.columns:
        fig, ax = plt.subplots(figsize=(7, 5))  # buat ukuran figure lebih besar
        sns.boxplot(x=y, y=data[fitur], ax=ax, palette=['#4c72b0', '#dd8452'])
        
        ax.set_xticklabels(['Tidak Diabetes (0)', 'Diabetes (1)'])
        ax.set_title(f'Distribusi {fitur.capitalize()} berdasarkan Kelas Diabetes', fontsize=14)
        ax.set_xlabel('Kelas Diabetes', fontsize=12)
        ax.set_ylabel(f'{fitur.capitalize()}', fontsize=12)
        
        # Optional: beri grid untuk membantu pembacaan nilai
        ax.grid(True, linestyle='--', alpha=0.7)
        
        st.pyplot(fig)


    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
    summaries = summarize_by_class(X_train, y_train)

    st.write("### Statistik Mean & Std per Kelas")
    for label, stats in summaries.items():
        st.write(f"**Kelas {label}**")
        st.write("Mean:", stats['mean'])
        st.write("Std:", stats['std'])

    joblib.dump({'summaries': summaries, 'dataset': data}, 'naive_bayes_modelGG.pkl')
    st.success("Model berhasil disimpan sebagai `naive_bayes_modelGG.pkl`")

    y_pred = [predict(summaries, row.values) for _, row in X_test.iterrows()]

    st.write("### Confusion Matrix")
    conf_matrix = confusion_matrix(y_test, y_pred)
    fig, ax = plt.subplots()
    sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues',
                xticklabels=['Tidak Diabetes', 'Diabetes'],
                yticklabels=['Tidak Diabetes', 'Diabetes'])
    ax.set_xlabel("Prediksi")
    ax.set_ylabel("Aktual")
    st.pyplot(fig)

    st.write("### Evaluasi Model")
    st.write(f"- Akurasi: **{accuracy_score(y_test, y_pred):.2f}**")
    st.write(f"- Precision: **{precision_score(y_test, y_pred):.2f}**")
    st.write(f"- Recall: **{recall_score(y_test, y_pred):.2f}**")
    st.write(f"- F1 Score: **{f1_score(y_test, y_pred):.2f}**")

    st.write("### K-Fold Cross Validation (5 Fold)")
    kfold = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    kfold_metrics = []

    for fold, (train_idx, test_idx) in enumerate(kfold.split(X, y), 1):
        X_tr, X_te = X.iloc[train_idx], X.iloc[test_idx]
        y_tr, y_te = y.iloc[train_idx], y.iloc[test_idx]
        summ_kf = summarize_by_class(X_tr, y_tr)
        y_pred_kf = [predict(summ_kf, row.values) for _, row in X_te.iterrows()]
        kfold_metrics.append({
            "Fold": fold,
            "Akurasi (%)": accuracy_score(y_te, y_pred_kf) * 100,
            "Precision (%)": precision_score(y_te, y_pred_kf, zero_division=0) * 100,
            "Recall (%)": recall_score(y_te, y_pred_kf, zero_division=0) * 100,
            "F1-Score (%)": f1_score(y_te, y_pred_kf, zero_division=0) * 100
        })

    df_kfold = pd.DataFrame(kfold_metrics)
    st.dataframe(df_kfold.style.format("{:.2f}"))
    st.write("#### Rata-rata K-Fold")
    st.write(f"- Akurasi: **{df_kfold['Akurasi (%)'].mean():.2f}%**")
    st.write(f"- Precision: **{df_kfold['Precision (%)'].mean():.2f}%**")
    st.write(f"- Recall: **{df_kfold['Recall (%)'].mean():.2f}%**")
    st.write(f"- F1 Score: **{df_kfold['F1-Score (%)'].mean():.2f}%**")

    # Form input data baru
    st.write("---")
    st.write("### Prediksi Data Baru")
    gdp = st.number_input("GDP (mg/dL)", 50, 300, 90)
    tekanan = st.number_input("Tekanan Darah (mmHg)", 60, 200, 120)
    imt = st.number_input("IMT", 10.0, 50.0, 22.0)
    umur = st.number_input("Umur (tahun)", 0, 120, 30)

    if st.button("Prediksi"):
        hasil = predict(summaries, [gdp, tekanan, imt, umur])
        st.success("Hasil: **Tidak Diabetes**" if hasil == 0 else "Hasil: **Diabetes**")

else:
    st.info("Silakan upload dataset CSV terlebih dahulu.")
