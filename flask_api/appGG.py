import streamlit as st
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.model_selection import train_test_split
from sklearn.metrics import confusion_matrix, accuracy_score, precision_score, recall_score, f1_score, classification_report, roc_auc_score
import joblib

# Fungsi untuk menghitung mean dan standar deviasi setiap fitur berdasarkan kelas
def summarize_by_class(X, y):
    summaries = {}
    for class_value in np.unique(y):
        class_data = X[y == class_value]
        summaries[class_value] = {
            'mean': class_data.mean(axis=0),
            'std': class_data.std(axis=0)
        }
    return summaries

# Fungsi untuk menghitung probabilitas Gaussian dengan handling zero std
def calculate_probability(x, mean, std):
    if std == 0:
        std = 1e-6
    exponent = np.exp(-((x - mean) ** 2) / (2 * std ** 2))
    return (1 / (np.sqrt(2 * np.pi) * std)) * exponent

# Fungsi untuk menghitung probabilitas tiap kelas
def calculate_class_probabilities(summaries, input_data):
    probabilities = {}
    for class_value, class_summaries in summaries.items():
        probabilities[class_value] = 1
        for i in range(len(input_data)):
            mean = class_summaries['mean'].values[i]
            std = class_summaries['std'].values[i]
            probabilities[class_value] *= calculate_probability(input_data[i], mean, std)
    return probabilities

# Fungsi prediksi
def predict(summaries, input_data):
    probabilities = calculate_class_probabilities(summaries, input_data)
    return max(probabilities, key=probabilities.get)

# UI Streamlit
st.title("🩺 Sistem Prediksi Diabetes Melitus Tipe 2")
st.subheader("🔍 Menggunakan Algoritma Naive Bayes (Perhitungan Manual)")

# Sidebar untuk upload dataset
st.sidebar.header("⚙️ Upload Dataset")
uploaded_file = st.sidebar.file_uploader("Upload file dataset CSV", type="csv")

if uploaded_file is not None:
    data = pd.read_csv(uploaded_file)
    st.write("### 🗂️ Data yang diupload:")
    st.dataframe(data.head())

    # Pisahkan fitur dan label
    X = data[['gdp', 'tekanan_darah', 'imt', 'umur']]
    y = data['hasil']

    # Distribusi Kelas
    st.write("### 📊 Distribusi Kelas")
    st.write(y.value_counts())

    # Korelasi Fitur
    st.write("### 🔗 Korelasi Antar Fitur")
    fig_corr, ax_corr = plt.subplots()
    sns.heatmap(data[['gdp', 'tekanan_darah', 'imt', 'umur']].corr(), annot=True, cmap='coolwarm', ax=ax_corr)
    st.pyplot(fig_corr)

    # Visualisasi distribusi fitur terhadap label
    st.write("### 📈 Distribusi Fitur terhadap Label")
    fitur_list = ['gdp', 'tekanan_darah', 'imt', 'umur']
    for fitur in fitur_list:
        st.write(f"#### Distribusi {fitur.capitalize()} berdasarkan Label")
        fig_feat, ax_feat = plt.subplots()
        sns.boxplot(x='hasil', y=fitur, data=data, ax=ax_feat)
        ax_feat.set_xticklabels(['Tidak Diabetes (0)', 'Diabetes (1)'])
        st.pyplot(fig_feat)


    # Split data untuk training dan testing
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    # Ringkasan statistik berdasarkan kelas
    summaries = summarize_by_class(X_train, y_train)

    # Tampilkan ringkasan mean & std tiap kelas
    st.write("### 📑 Ringkasan Mean & Std per Kelas")
    for class_value, stats in summaries.items():
        st.write(f"**Kelas {class_value}**")
        st.write("Mean:")
        st.write(stats['mean'])
        st.write("Std:")
        st.write(stats['std'])

    # Simpan model dan dataset
    model_and_data = {'summaries': summaries, 'dataset': data}
    joblib.dump(model_and_data, 'naive_bayes_modelGG.pkl')
    st.success("✅ Model dan dataset berhasil disimpan sebagai **'naive_bayes_modelGG.pkl'**")

    # Prediksi data testing
    y_pred = [predict(summaries, row.values) for _, row in X_test.iterrows()]

    # Evaluasi model
    conf_matrix = confusion_matrix(y_test, y_pred)
    accuracy = accuracy_score(y_test, y_pred)
    precision = precision_score(y_test, y_pred)
    recall = recall_score(y_test, y_pred)
    f1 = f1_score(y_test, y_pred)

    # Estimasi ROC-AUC
    probs = []
    for _, row in X_test.iterrows():
        probas = calculate_class_probabilities(summaries, row.values)
        probs.append(probas[1] / sum(probas.values()))
    auc = roc_auc_score(y_test, probs)

    # Tampilkan Metrik Evaluasi
    st.write("### 🧮 Evaluasi Model")
    st.write(f"- **Akurasi Model:** {accuracy * 100:.2f}%")
    st.write(f"- **Precision:** {precision * 100:.2f}%")
    st.write(f"- **Recall:** {recall * 100:.2f}%")
    st.write(f"- **F1-Score:** {f1 * 100:.2f}%")
    st.write(f"- **ROC-AUC Score:** {auc:.2f}")

    # Classification Report
    st.write("### 📝 Classification Report")
    st.text(classification_report(y_test, y_pred, target_names=['Tidak Diabetes', 'Diabetes']))

    # Tampilkan Confusion Matrix
    st.write("### 🧩 Confusion Matrix")
    fig, ax = plt.subplots()
    sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues',
                xticklabels=['Tidak Diabetes', 'Diabetes'],
                yticklabels=['Tidak Diabetes', 'Diabetes'])
    plt.xlabel('Prediksi')
    plt.ylabel('Aktual')
    st.pyplot(fig)

    # Form untuk input data baru
    st.write("---")
    st.write("### 🚀 Prediksi Data Baru")
    gdp = st.number_input("GDP (Gula Darah Puasa) [mg/dL]", min_value=50, max_value=300, value=90, help="Biasanya antara 70-125 mg/dL untuk normal")
    tekanan_darah = st.number_input("Tekanan Darah [mmHg]", min_value=60, max_value=200, value=120, help="Normal biasanya 90-120 mmHg")
    imt = st.number_input("IMT (Indeks Massa Tubuh)", min_value=10.0, max_value=50.0, value=22.0, help="Normal antara 18.5 - 24.9")
    umur = st.number_input("Umur [tahun]", min_value=0, max_value=120, value=30, help="Masukkan usia pasien")

    if st.button("🔮 Prediksi"):
        new_data = [gdp, tekanan_darah, imt, umur]
        prediction = predict(summaries, new_data)
        if prediction == 0:
            st.success("Hasil Prediksi: **Tidak Diabetes**")
        else:
            st.error("Hasil Prediksi: **Diabetes**")
else:
    st.info("📥 Silakan upload dataset CSV terlebih dahulu.")
