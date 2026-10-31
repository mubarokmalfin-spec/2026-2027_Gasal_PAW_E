-- MODULE 5 : DATABASE | Database: penjualan
CREATE DATABASE IF NOT EXISTS penjualan CHARACTER SET utf8mb4;
USE penjualan;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS transaksi_detail, pembayaran, transaksi, barang, supplier, pelanggan, user;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE pelanggan (
  id varchar(20) NOT NULL PRIMARY KEY,
  nama varchar(100) NOT NULL,
  jenis_kelamin enum('L','P') NOT NULL,
  telp varchar(12) NOT NULL,
  alamat text NOT NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama varchar(100) NOT NULL,
  telp varchar(12) NOT NULL,
  alamat text NOT NULL
) ENGINE=InnoDB;

CREATE TABLE barang (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  kode_barang varchar(10) NOT NULL,
  nama_barang varchar(100) NOT NULL,
  harga int(11) NOT NULL,
  stok int(11) NOT NULL,
  supplier_id int(11) NULL,
  FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaksi (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  waktu_transaksi date NOT NULL,
  keterangan text,
  total int(11) NOT NULL,
  pelanggan_id varchar(20) NOT NULL,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaksi_detail (
  transaksi_id int(11) NOT NULL,
  barang_id int(11) NOT NULL,
  harga int(11) NOT NULL,
  qty int(11) NOT NULL,
  PRIMARY KEY (transaksi_id, barang_id),
  FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  waktu_bayar datetime NOT NULL,
  total int(11) NOT NULL,
  metode enum('TUNAI','TRANSFER','EDC') NOT NULL,
  transaksi_id int(11) NOT NULL,
  FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user (
  id_user tinyint(2) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username varchar(30) NOT NULL,
  password varchar(35) NOT NULL,
  nama varchar(50) NOT NULL,
  alamat varchar(150) NOT NULL,
  hp varchar(20) NOT NULL,
  level tinyint(1) NOT NULL
) ENGINE=InnoDB;

-- ===== DATA (minimal 10 per tabel) =====
INSERT INTO supplier (nama,telp,alamat) VALUES
('PT. Maju Bersama','08123456','Surabaya'),
('PT. Senang Sekali','081515563','Bangkalan'),
('PT. Segar Segar','0845454663','Surabaya'),
('PT. Jaya Abadi','081234567','Sidoarjo'),
('CV. Sumber Makmur','082233445','Gresik'),
('PT. Sinar Terang','085566778','Malang'),
('CV. Berkah Sentosa','081377889','Mojokerto'),
('PT. Mitra Sejahtera','087788990','Surabaya'),
('CV. Cahaya Baru','089900112','Lamongan'),
('PT. Karya Mandiri','081122334','Pasuruan');

INSERT INTO pelanggan (id,nama,jenis_kelamin,telp,alamat) VALUES
('PLG001','Budi Santoso','L','081210001','Surabaya'),
('PLG002','Siti Aminah','P','081210002','Sidoarjo'),
('PLG003','Agus Prasetyo','L','081210003','Gresik'),
('PLG004','Dewi Lestari','P','081210004','Malang'),
('PLG005','Rudi Hartono','L','081210005','Bangkalan'),
('PLG006','Rina Wulandari','P','081210006','Surabaya'),
('PLG007','Hendra Wijaya','L','081210007','Mojokerto'),
('PLG008','Lia Permata','P','081210008','Lamongan'),
('PLG009','Doni Kurniawan','L','081210009','Pasuruan'),
('PLG010','Maya Sari','P','081210010','Surabaya');

INSERT INTO barang (kode_barang,nama_barang,harga,stok,supplier_id) VALUES
('BRG001','Beras 5 Kg',65000,100,1),
('BRG002','Minyak Goreng 2 L',34000,80,2),
('BRG003','Gula Pasir 1 Kg',16000,120,3),
('BRG004','Telur Ayam 1 Kg',28000,60,4),
('BRG005','Tepung Terigu 1 Kg',12000,90,5),
('BRG006','Susu UHT 1 L',18000,70,6),
('BRG007','Kopi Bubuk 200 g',22000,50,7),
('BRG008','Teh Celup 25 pcs',9000,150,8),
('BRG009','Mie Instan',3500,300,9),
('BRG010','Sabun Mandi',4500,200,10);

INSERT INTO transaksi (waktu_transaksi,keterangan,total,pelanggan_id) VALUES
('2024-05-01','Pembelian bulanan',99000,'PLG001'),
('2024-05-02','Pembelian harian',34000,'PLG002'),
('2024-05-03','Belanja dapur',44000,'PLG003'),
('2024-05-04','Pembelian harian',28000,'PLG004'),
('2024-05-05','Stok warung',130000,'PLG005'),
('2024-05-06','Belanja dapur',36000,'PLG006'),
('2024-05-07','Pembelian harian',22000,'PLG007'),
('2024-05-08','Stok warung',45000,'PLG008'),
('2024-05-09','Pembelian harian',35000,'PLG009'),
('2024-05-10','Belanja bulanan',54000,'PLG010');

INSERT INTO transaksi_detail (transaksi_id,barang_id,harga,qty) VALUES
(1,1,65000,1),(1,2,34000,1),(2,2,34000,1),(3,3,16000,1),
(3,4,28000,1),(4,4,28000,1),(5,1,65000,2),(6,6,18000,2),
(7,7,22000,1),(8,8,9000,5),(9,9,3500,10),(10,10,4500,12);

INSERT INTO pembayaran (waktu_bayar,total,metode,transaksi_id) VALUES
('2024-05-01 09:10:00',99000,'TUNAI',1),
('2024-05-02 10:15:00',34000,'TRANSFER',2),
('2024-05-03 11:20:00',44000,'EDC',3),
('2024-05-04 12:05:00',28000,'TUNAI',4),
('2024-05-05 13:30:00',130000,'TRANSFER',5),
('2024-05-06 14:45:00',36000,'EDC',6),
('2024-05-07 15:10:00',22000,'TUNAI',7),
('2024-05-08 16:00:00',45000,'TRANSFER',8),
('2024-05-09 16:40:00',35000,'EDC',9),
('2024-05-10 17:25:00',54000,'TUNAI',10);

INSERT INTO user (username,password,nama,alamat,hp,level) VALUES
('admin',MD5('admin123'),'Administrator','Surabaya','081300001',1),
('kasir1',MD5('kasir123'),'Kasir Satu','Sidoarjo','081300002',2),
('kasir2',MD5('kasir123'),'Kasir Dua','Gresik','081300003',2),
('gudang1',MD5('gudang123'),'Staf Gudang','Surabaya','081300004',3),
('gudang2',MD5('gudang123'),'Staf Gudang Dua','Malang','081300005',3),
('manager',MD5('manager123'),'Manager Toko','Surabaya','081300006',1),
('sales1',MD5('sales123'),'Sales Satu','Bangkalan','081300007',2),
('sales2',MD5('sales123'),'Sales Dua','Lamongan','081300008',2),
('owner',MD5('owner123'),'Pemilik Toko','Surabaya','081300009',1),
('magang',MD5('magang123'),'Anak Magang','Pasuruan','081300010',3);
