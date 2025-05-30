# tests/test_app.py
import pytest
import pandas as pd
import numpy as np

# Impor fungsi-fungsi dari file aplikasi Streamlit Anda
from streamlit_app import (
    calculate_probability,
    summarize_by_class,
    calculate_class_probabilities,
    predict,
    predict_proba
)

# --- 1. Tes untuk Fungsi Individual (Unit Tests) ---

def test_calculate_probability():
    """Menguji fungsi calculate_probability dengan input normal dan kasus khusus."""
    # Kasus Normal
    assert calculate_probability(x=50, mean=50, std=5) > 0
    # Kasus di mana std adalah 0 (untuk menghindari pembagian dengan nol)
    assert calculate_probability(x=50, mean=50, std=0) > 0


def test_summarize_by_class():
    """Menguji fungsi summarize_by_class untuk menghitung mean, std, dan prior."""
    # Membuat DataFrame dummy untuk pengujian
    data = {
        'fitur1': [10, 12, 11, 30, 32],
        'fitur2': [5, 6, 4, 15, 16],
        'hasil':  [0, 0, 0, 1, 1]
    }
    df = pd.DataFrame(data)
    X = df[['fitur1', 'fitur2']]
    y = df['hasil']

    summaries = summarize_by_class(X, y)

    # Memeriksa apakah semua kelas ada
    assert 0 in summaries
    assert 1 in summaries

    # Memeriksa prior probability
    # Kelas 0 muncul 3 dari 5 kali
    assert summaries[0]['prior'] == pytest.approx(3/5)
    # Kelas 1 muncul 2 dari 5 kali
    assert summaries[1]['prior'] == pytest.approx(2/5)

    # Memeriksa mean untuk kelas 0
    assert summaries[0]['mean']['fitur1'] == pytest.approx(np.mean([10, 12, 11]))
    # Memeriksa std untuk kelas 1
    assert summaries[1]['std']['fitur2'] == pytest.approx(np.std([15, 16], ddof=1)) # ddof=1 sama seperti pandas .std()


def test_predict_and_proba():
    """Menguji fungsi predict dan predict_proba."""
    # Membuat summaries dummy yang sangat jelas
    summaries = {
        0: { # Kelas 0 sangat mungkin
            'prior': 0.5,
            'mean': pd.Series([10.0, 10.0]),
            'std': pd.Series([1.0, 1.0])
        },
        1: { # Kelas 1 tidak mungkin
            'prior': 0.5,
            'mean': pd.Series([100.0, 100.0]),
            'std': pd.Series([1.0, 1.0])
        }
    }
    input_data_cocok_kelas0 = [10.0, 10.0]

    # Tes predict harus memilih kelas 0
    prediction = predict(summaries, input_data_cocok_kelas0)
    assert prediction == 0

    # Tes predict_proba harus memberikan probabilitas tinggi untuk kelas 1 (jika positive_class_label=1)
    # Tapi karena inputnya cocok kelas 0, probabilitas kelas 1 harusnya rendah
    prob_class1 = predict_proba(summaries, input_data_cocok_kelas0, positive_class_label=1)
    assert 0 <= prob_class1 <= 1
    assert prob_class1 < 0.5 # Karena input sangat cocok dengan kelas 0

    # Tes kasus di mana probabilitas total adalah nol
    prob_zero_evidence = predict_proba(summaries, [999, 999], positive_class_label=1)
    # Harus mengembalikan nilai default (1/jumlah_kelas) jika evidence=0
    assert prob_zero_evidence == pytest.approx(1.0 / 2)


# --- 2. Tes untuk Alur Aplikasi (Integration Test) ---

# Fixture untuk memuat data dummy sekali untuk semua tes di bawah ini
@pytest.fixture
def dummy_data():
    """Memuat data dari dummy_data.csv."""
    try:
        # Asumsikan file tes dijalankan dari direktori root (proyek_streamlit/)
        # Jika tidak, sesuaikan path ini
        return pd.read_csv("tests/dummy_data.csv")
    except FileNotFoundError:
        pytest.fail("File 'tests/dummy_data.csv' tidak ditemukan. Pastikan Anda membuatnya.")

def test_full_process_with_data(dummy_data):
    """
    Menguji alur kerja utama: memuat data, melatih (summarize), dan memprediksi.
    Ini mensimulasikan apa yang terjadi di dalam blok 'if uploaded_file:'
    """
    # 1. Memisahkan fitur (X) dan target (y)
    required_columns = ['gdp', 'tekanan_darah', 'imt', 'umur', 'hasil']
    assert all(col in dummy_data.columns for col in required_columns)
    
    X = dummy_data[['gdp', 'tekanan_darah', 'imt', 'umur']]
    y = dummy_data['hasil']

    # 2. "Melatih" model -> Menghitung summaries
    # Kita gunakan semua data sebagai data training untuk kesederhanaan tes ini
    summaries = summarize_by_class(X, y)

    assert summaries is not None
    assert 0 in summaries
    assert 1 in summaries
    assert 'mean' in summaries[0]
    assert 'std' in summaries[1]

    # 3. Melakukan prediksi pada satu baris data
    # Ambil baris pertama dari data kita, yang seharusnya kelas 0
    first_row_data = X.iloc[0].values
    prediction = predict(summaries, first_row_data)

    assert prediction in [0, 1] # Hasilnya harus berupa label kelas

    # 4. Melakukan prediksi probabilitas pada satu baris data
    proba = predict_proba(summaries, first_row_data, positive_class_label=1)
    assert 0 <= proba <= 1 # Hasilnya harus berupa probabilitas yang valid

