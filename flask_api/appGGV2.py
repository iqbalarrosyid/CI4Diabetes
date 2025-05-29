import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt # Untuk ROC Curve
from sklearn.model_selection import train_test_split, StratifiedKFold
from sklearn.metrics import (
    accuracy_score, roc_auc_score, roc_curve
)
# import joblib # Opsional jika ingin tetap menyimpan model

# ==================== FUNGSI NAIVE BAYES (TIDAK DIUBAH) ====================
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

def predict_proba(summaries, input_data, positive_class_label=1):
    class_likelihood_times_priors = calculate_class_probabilities(summaries, input_data)
    prob_positive_numerator = class_likelihood_times_priors.get(positive_class_label, 0.0)
    evidence_px = sum(class_likelihood_times_priors.values())
    if evidence_px == 0:
        num_classes = len(summaries)
        return 1.0 / num_classes if num_classes > 0 else 0.0
    posterior_prob_positive = prob_positive_numerator / evidence_px
    return posterior_prob_positive

# ==================== STREAMLIT APP (Lebih Ringkas) ====================
st.title("🩺 Prediksi Diabetes Tipe 2 (Manual Naive Bayes)")

uploaded_file = st.sidebar.file_uploader("Upload file CSV", type="csv")

if uploaded_file:
    data = pd.read_csv(uploaded_file)
    st.write("### Data Awal (5 baris pertama):")
    st.dataframe(data.head())

    # Asumsi kolom yang dibutuhkan ada dan benar
    try:
        X = data[['gdp', 'tekanan_darah', 'imt', 'umur']]
        y = data['hasil']
    except KeyError as e:
        st.error(f"Kolom tidak ditemukan: {e}. Pastikan dataset memiliki kolom 'gdp', 'tekanan_darah', 'imt', 'umur', dan 'hasil'.")
        st.stop() # Hentikan eksekusi jika kolom tidak ada

    # 1. Pembagian Data
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
    st.write(f"Data dibagi: {len(X_train)} baris training, {len(X_test)} baris testing.")

    # 2. Pelatihan Model (Menghitung Ringkasan Statistik)
    summaries = summarize_by_class(X_train, y_train)
    st.write("### Ringkasan Statistik Model (dari Data Training):")
    # (Untuk keringkasan, detail mean/std per kelas tidak ditampilkan di sini, tapi 'summaries' sudah dihitung)
    st.caption("Mean, Std Dev, dan Prior per kelas telah dihitung.")


    # 3. Prediksi pada Data Testing
    # Prediksi label kelas
    y_pred_labels = [predict(summaries, row.values) for _, row in X_test.iterrows()]
    # Prediksi probabilitas untuk kelas positif (untuk AUC)
    y_pred_probabilities = [predict_proba(summaries, row.values, positive_class_label=1) for _, row in X_test.iterrows()]

    # 4. Evaluasi Model
    st.write("### Hasil Evaluasi pada Data Testing:")
    accuracy = accuracy_score(y_test, y_pred_labels)
    st.write(f"- Akurasi: **{accuracy:.2f}**")
    try:
        auc = roc_auc_score(y_test, y_pred_probabilities)
        st.write(f"- AUC: **{auc:.2f}**")

        # Plot ROC Curve sederhana
        fpr, tpr, _ = roc_curve(y_test, y_pred_probabilities)
        fig_roc, ax_roc = plt.subplots()
        ax_roc.plot(fpr, tpr, color='blue', lw=2, label=f'ROC curve (AUC = {auc:.2f})')
        ax_roc.plot([0, 1], [0, 1], color='red', lw=2, linestyle='--', label='Random (AUC = 0.50)')
        ax_roc.set_xlabel('False Positive Rate')
        ax_roc.set_ylabel('True Positive Rate')
        ax_roc.set_title('ROC Curve')
        ax_roc.legend(loc="lower right")
        st.pyplot(fig_roc)

    except ValueError as e:
        st.warning(f"Tidak dapat menghitung AUC: {e}")


    # (Bagian K-Fold Cross Validation dan Prediksi Data Baru bisa ditambahkan kembali jika diperlukan,
    #  namun untuk fokus pada alur dasar Naive Bayes, sengaja diringkas)

    # 5. Prediksi Data Baru (Contoh Sederhana)
    st.write("---")
    st.write("### 🚀 Prediksi Data Baru (Contoh Input):")
    # Gunakan nilai default atau biarkan user input sederhana
    gdp_input = st.number_input("GDP (mg/dL)", value=100)
    td_input = st.number_input("Tekanan Darah (mmHg)", value=120)
    imt_input = st.number_input("IMT (kg/m²)", value=25.0, format="%.1f")
    umur_input = st.number_input("Umur (tahun)", value=35)

    if st.button("Prediksi Data Baru"):
        input_baru = [gdp_input, td_input, imt_input, umur_input]
        hasil_prediksi_label = predict(summaries, input_baru)
        hasil_prediksi_proba = predict_proba(summaries, input_baru, positive_class_label=1)

        st.write(f"Prediksi Kelas: **{'Diabetes' if hasil_prediksi_label == 1 else 'Tidak Diabetes'}**")
        st.write(f"Probabilitas Diabetes: **{hasil_prediksi_proba:.2f}**")

else:
    st.info("Silakan unggah dataset CSV untuk memulai.")