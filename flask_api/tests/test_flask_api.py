# tests/test_flask_api.py
import pytest
import json

# Impor 'app' dari file Flask Anda.
# Jika nama file Flask Anda adalah app.py, ini sudah benar.
from app import app

# Fixture ini akan menyiapkan 'test client' untuk setiap fungsi tes.
# 'test client' ini memungkinkan kita mengirim request ke API tanpa menjalankan server sungguhan.
@pytest.fixture
def client():
    """Membuat dan mengkonfigurasi test client untuk setiap tes."""
    app.config['TESTING'] = True  # Mengaktifkan mode tes pada Flask
    with app.test_client() as client:
        yield client

# === KUMPULAN TES UNTUK ENDPOINT /predict ===

def test_prediksi_sukses(client):
    """
    Tes Skenario 1: Mengirim data yang valid dan mengharapkan prediksi yang berhasil.
    """
    # PENTING: Ganti nilai di bawah ini dengan data yang Anda tahu hasilnya
    # berdasarkan model asli Anda.
    data_input_valid = {
        'imt': 25.5,
        'umur': 45,
        'gdp': 130,
        'tekanan_darah': 140
    }

    # Anda perlu tahu hasil yang diharapkan untuk data di atas.
    # Misalnya, jika Anda tahu hasilnya adalah 1 (Diabetes), maka set variabel di bawah ini.
    hasil_yang_diharapkan = 1

    # Mengirim request POST ke endpoint /predict
    response = client.post('/predict', json=data_input_valid)

    # 1. Memeriksa apakah status code respons adalah 200 OK
    assert response.status_code == 200

    # 2. Mengambil data JSON dari respons
    data_respons = response.get_json()

    # 3. Memeriksa apakah respons berisi 'success: true'
    assert data_respons['success'] is True
    
    # 4. Memeriksa apakah ada kunci 'outcome' dalam respons
    assert 'outcome' in data_respons
    
    # 5. Memeriksa apakah hasil prediksi sesuai dengan yang diharapkan
    assert data_respons['outcome'] == hasil_yang_diharapkan


def test_prediksi_data_tidak_lengkap(client):
    """
    Tes Skenario 2: Mengirim data yang tidak lengkap (salah satu field hilang).
    """
    # Data di mana 'gdp' sengaja dihilangkan
    data_input_tidak_lengkap = {
        'imt': 25.5,
        'umur': 45,
        # 'gdp': 130, --> Dihilangkan
        'tekanan_darah': 140
    }

    response = client.post('/predict', json=data_input_tidak_lengkap)
    data_respons = response.get_json()

    # 1. Status code tetap 200 karena error ditangani dengan baik
    assert response.status_code == 200

    # 2. Memeriksa apakah respons berisi 'success: false'
    assert data_respons['success'] is False

    # 3. Memeriksa apakah pesan error sesuai dengan yang ada di kode Anda
    assert data_respons['message'] == 'Data yang diterima tidak lengkap atau salah tipe'


def test_prediksi_tipe_data_salah(client):
    """
    Tes Skenario 3: Mengirim data dengan tipe yang salah (string di field angka).
    Ini akan menguji blok 'try-except' di kode Anda.
    """
    data_input_tipe_salah = {
        'imt': 25.5,
        'umur': 'empat puluh lima', # Ini akan menyebabkan error saat konversi float()
        'gdp': 130,
        'tekanan_darah': 140
    }

    response = client.post('/predict', json=data_input_tipe_salah)
    data_respons = response.get_json()

    # 1. Status code tetap 200
    assert response.status_code == 200

    # 2. Memeriksa apakah 'success' adalah false
    assert data_respons['success'] is False

    # 3. Memeriksa apakah pesan error mengandung teks dari exception
    # Pesan dari float('empat puluh lima') biasanya "could not convert string to float"
    assert "could not convert string to float" in data_respons['message']