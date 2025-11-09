-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 08:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `datlichkham`
--

-- --------------------------------------------------------

--
-- Table structure for table `bacsi`
--

CREATE TABLE `bacsi` (
  `nguoiDungId` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `tenBacSi` varchar(100) DEFAULT NULL,
  `maChuyenKhoa` varchar(10) DEFAULT NULL,
  `moTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bacsi`
--

INSERT INTO `bacsi` (`nguoiDungId`, `maBacSi`, `tenBacSi`, `maChuyenKhoa`, `moTa`) VALUES
(2, 'bs1', 'Trần Văn B', 'EYE102501', NULL),
(5, 'BS202511090112882', 'Nguyễn Thành C', 'SUR102501', NULL),
(7, 'BS202511090152610', 'Lê Văn D', 'INT102503', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `benhnhan`
--

CREATE TABLE `benhnhan` (
  `nguoiDungId` int(11) NOT NULL,
  `maBenhNhan` varchar(20) NOT NULL,
  `tenBenhNhan` varchar(100) DEFAULT NULL,
  `ngaySinh` date DEFAULT NULL,
  `gioiTinh` enum('nam','nu','khac') DEFAULT NULL,
  `soTheBHYT` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `benhnhan`
--

INSERT INTO `benhnhan` (`nguoiDungId`, `maBenhNhan`, `tenBenhNhan`, `ngaySinh`, `gioiTinh`, `soTheBHYT`) VALUES
(1, 'bn1', 'Nguyễn Văn A', '2000-01-01', 'nam', NULL),
(8, 'BN202511082304701', 'ABCs', '2005-10-09', 'nam', ''),
(9, 'BN202511090811622', 'A', '2025-11-15', 'nam', '');

-- --------------------------------------------------------

--
-- Table structure for table `chuyenkhoa`
--

CREATE TABLE `chuyenkhoa` (
  `maChuyenKhoa` varchar(10) NOT NULL,
  `tenChuyenKhoa` varchar(100) NOT NULL,
  `maKhoa` varchar(10) DEFAULT NULL,
  `moTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chuyenkhoa`
--

INSERT INTO `chuyenkhoa` (`maChuyenKhoa`, `tenChuyenKhoa`, `maKhoa`, `moTa`) VALUES
('CAR102501', 'Tim Mạch Nội Khoa', 'CAR1025', 'Chẩn đoán và điều trị các bệnh tim bằng thuốc, như tăng huyết áp, rối loạn nhịp tim, suy tim. Khoa chú trọng điều trị lâu dài và phòng ngừa tái phát.'),
('CAR102502', 'Tim Mạch Can Thiệp', 'CAR1025', 'Thực hiện các thủ thuật như nong mạch, đặt stent, chụp mạch vành. Khoa phối hợp chặt chẽ với nội tim mạch để quản lý bệnh nhân sau can thiệp.'),
('DEN102501', 'Nha Khoa Tổng Quát', 'DEN1025', 'Khám, trám, nhổ răng và điều trị sâu răng. Khoa tiếp nhận hầu hết các ca bệnh răng miệng thông thường.'),
('DEN102502', 'Chỉnh Nha', 'DEN1025', 'Điều chỉnh vị trí răng, khớp cắn bằng khí cụ hoặc niềng răng. Khoa giúp cải thiện cả chức năng nhai và thẩm mỹ.'),
('DEN102503', 'Phẫu Thuật Hàm Mặt', 'DEN1025', 'Thực hiện các ca phẫu thuật chấn thương, dị tật và thẩm mỹ vùng mặt. Khoa kết hợp với chuyên khoa tai – mũi – họng khi cần thiết.'),
('DER102501', 'Da Liễu Tổng Quát', 'DER1025', 'Khám và điều trị các bệnh ngoài da như viêm da, mụn, nấm và dị ứng. Khoa áp dụng phác đồ hiện đại để cải thiện tình trạng da.'),
('DER102502', 'Da Liễu Thẩm Mỹ', 'DER1025', 'Cung cấp các dịch vụ chăm sóc và điều trị thẩm mỹ da như laser, trị sẹo, giảm nám. Khoa đảm bảo an toàn và hiệu quả cho từng loại da.'),
('ENT102501', 'Tai Học', 'ENT1025', 'Chẩn đoán và điều trị các bệnh về tai như viêm tai, thủng màng nhĩ, giảm thính lực. Khoa cung cấp dịch vụ đo thính lực và phục hồi thính giác.'),
('ENT102502', 'Mũi Xoang', 'ENT1025', 'Điều trị viêm mũi dị ứng, viêm xoang và polyp mũi. Khoa sử dụng nội soi để chẩn đoán và phẫu thuật chính xác.'),
('ENT102503', 'Họng – Thanh Quản', 'ENT1025', 'Khám và điều trị các bệnh về họng, thanh quản như khàn tiếng, viêm amidan. Khoa cũng thực hiện các phẫu thuật nhỏ như cắt amidan hoặc polyp dây thanh.'),
('EYE102501', 'Khúc Xạ', 'EYE1025', 'Chẩn đoán và điều trị các tật khúc xạ như cận, viễn, loạn thị. Khoa cũng tư vấn sử dụng kính và phẫu thuật laser điều chỉnh thị lực.'),
('EYE102502', 'Mắt Nội Khoa', 'EYE1025', 'Điều trị các bệnh lý như viêm kết mạc, tăng nhãn áp, thoái hóa điểm vàng. Khoa chú trọng phát hiện sớm để ngăn ngừa mất thị lực.'),
('EYE102503', 'Mắt Phẫu Thuật', 'EYE1025', 'Thực hiện các ca phẫu thuật mắt như đục thủy tinh thể, mổ lé và ghép giác mạc. Các bác sĩ được đào tạo chuyên sâu về vi phẫu.'),
('INT102501', 'Nội Hô Hấp', 'INT1025', 'Chuyên khoa Nội hô hấp điều trị các bệnh về đường hô hấp như viêm phổi, hen suyễn, COPD. Khoa cung cấp các dịch vụ chẩn đoán chuyên sâu như đo chức năng hô hấp và nội soi phế quản.'),
('INT102502', 'Nội Tiêu Hóa', 'INT1025', 'Chuyên khoa Nội tiêu hóa tập trung chẩn đoán và điều trị bệnh dạ dày, gan, mật, ruột. Các kỹ thuật nội soi và xét nghiệm sinh hóa thường được sử dụng.'),
('INT102503', 'Nội Thần Kinh', 'INT1025', 'Chuyên khoa Nội thần kinh điều trị các bệnh về não và hệ thần kinh như đột quỵ, động kinh, Parkinson. Khoa phối hợp với chẩn đoán hình ảnh để theo dõi tiến triển bệnh.'),
('INT102504', 'Nội Tiết', 'INT1025', 'Chuyên khoa Nội tiết chuyên điều trị các rối loạn hormone như tiểu đường, cường giáp, béo phì. Khoa thường theo dõi bệnh nhân lâu dài để kiểm soát bệnh mạn tính.'),
('INT102505', 'Nội Thận – Tiết Niệu', 'INT1025', 'Chuyên khoa này điều trị các bệnh lý về thận và đường tiết niệu không cần phẫu thuật. Bao gồm viêm cầu thận, suy thận, sỏi thận giai đoạn nhẹ.'),
('INT102506', 'Nội Tổng Hợp', 'INT1025', 'Chuyên khoa Nội tổng hợp tiếp nhận và điều trị các bệnh thông thường và chưa xác định rõ chuyên khoa. Đây là nơi bệnh nhân được thăm khám tổng quát và phân tuyến điều trị.'),
('OBG102501', 'Sản Khoa', 'OBG1025', 'Theo dõi thai kỳ, đỡ sinh, mổ lấy thai và chăm sóc hậu sản. Khoa đảm bảo an toàn cho cả mẹ và bé trong suốt quá trình mang thai và sinh nở.'),
('OBG102502', 'Phụ Khoa', 'OBG1025', 'Điều trị các bệnh lý phụ khoa như viêm nhiễm, u xơ, rối loạn kinh nguyệt. Ngoài ra còn tư vấn sức khỏe sinh sản cho phụ nữ.'),
('PED102501', 'Nhi Tổng Quát', 'PED1025', 'Khám và điều trị các bệnh thường gặp ở trẻ em như sốt, ho, tiêu chảy, viêm phổi. Khoa đảm bảo theo dõi và tư vấn dinh dưỡng cho trẻ.'),
('PED102502', 'Nhi Sơ Sinh', 'PED1025', 'Chăm sóc và điều trị các bệnh lý ở trẻ sơ sinh, đặc biệt là trẻ sinh non. Khoa có trang thiết bị hỗ trợ hô hấp và nuôi dưỡng đặc biệt.'),
('PED102503', 'Nhi Tim Mạch', 'PED1025', 'Chẩn đoán và điều trị bệnh tim bẩm sinh ở trẻ em. Khoa phối hợp với khoa tim mạch để theo dõi và can thiệp sớm.'),
('SUR102501', 'Ngoại Tổng Hợp', 'SUR1025', 'Chuyên khoa Ngoại tổng hợp thực hiện các phẫu thuật phổ biến như ruột thừa, thoát vị, sỏi mật. Đây là chuyên khoa đa dạng, tiếp nhận nhiều loại bệnh lý khác nhau.'),
('SUR102502', 'Ngoại Chấn Thương Chỉnh Hình', 'SUR1025', 'Điều trị gãy xương, trật khớp và các chấn thương cơ xương khớp. Khoa thường phối hợp với vật lý trị liệu để phục hồi vận động.'),
('SUR102503', 'Ngoại Thần Kinh', 'SUR1025', 'Thực hiện phẫu thuật liên quan đến não, cột sống và tủy sống. Khoa xử lý các ca chấn thương sọ não, u não và thoát vị đĩa đệm.'),
('SUR102504', 'Ngoại Tiết Niệu', 'SUR1025', 'Điều trị bằng phẫu thuật cho các bệnh lý thận, bàng quang và tuyến tiền liệt. Khoa ứng dụng công nghệ nội soi và laser trong điều trị.'),
('SUR102505', 'Ngoại Lồng Ngực – Tim Mạch', 'SUR1025', 'Chuyên về phẫu thuật tim, phổi và mạch máu lớn. Các ca mổ đòi hỏi kỹ thuật cao được thực hiện tại đây.');

-- --------------------------------------------------------

--
-- Table structure for table `hosobenhan`
--

CREATE TABLE `hosobenhan` (
  `maHoSo` varchar(20) NOT NULL,
  `maBenhNhan` varchar(20) DEFAULT NULL,
  `maBacSi` varchar(20) DEFAULT NULL,
  `chanDoan` text DEFAULT NULL,
  `dieuTri` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hosobenhan`
--

INSERT INTO `hosobenhan` (`maHoSo`, `maBenhNhan`, `maBacSi`, `chanDoan`, `dieuTri`) VALUES
('HS1', 'bn1', 'bs1', 'Tình trạng bình thường.', 'Ăn uống ngủ nghỉ hợp lý.');

-- --------------------------------------------------------

--
-- Table structure for table `khoa`
--

CREATE TABLE `khoa` (
  `maKhoa` varchar(10) NOT NULL,
  `tenKhoa` varchar(100) NOT NULL,
  `moTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khoa`
--

INSERT INTO `khoa` (`maKhoa`, `tenKhoa`, `moTa`) VALUES
('CAR1025', 'Khoa Tim mạch', 'Khoa Tim mạch tập trung khám và điều trị các bệnh về tim và mạch máu. Đây là khoa chuyên sâu trong chẩn đoán, theo dõi và phục hồi chức năng tim mạch.'),
('DEN1025', 'Khoa Răng – Hàm – Mặt', 'Khoa này chuyên điều trị các bệnh lý răng miệng, hàm và vùng mặt. Ngoài điều trị, khoa còn thực hiện phẫu thuật thẩm mỹ và chỉnh hình hàm mặt.'),
('DER1025', 'Khoa Da liễu', 'Khoa Da liễu chuyên khám và điều trị các bệnh ngoài da, tóc, móng. Ngoài điều trị bệnh, khoa còn cung cấp dịch vụ thẩm mỹ da như laser, trị sẹo và nám.'),
('ENT1025', 'Khoa Tai – Mũi – Họng', 'Khoa Tai - Mũi - Họng chẩn đoán và điều trị các bệnh lý về tai, mũi, họng và thanh quản. Khoa cũng đảm nhận các ca phẫu thuật nhỏ trong khu vực đầu – cổ.'),
('EYE1025', 'Khoa Mắt', 'Khoa Mắt chịu trách nhiệm khám và điều trị các bệnh về mắt và thị lực. Ngoài ra còn thực hiện các phẫu thuật khúc xạ, đục thủy tinh thể và các tật bẩm sinh.'),
('INT1025', 'Khoa Nội', 'Khoa Nội chuyên khám và điều trị các bệnh lý bên trong cơ thể mà không cần phẫu thuật. Đây là nơi tập trung nhiều chuyên khoa như hô hấp, tiêu hóa, thần kinh và nội tổng hợp.'),
('OBG1025', 'Khoa Sản', 'Khoa Sản theo dõi, chăm sóc và điều trị cho phụ nữ trong thời kỳ mang thai và sau sinh. Ngoài ra, khoa còn hỗ trợ điều trị các bệnh lý phụ khoa thường gặp.'),
('PED1025', 'Khoa Nhi', 'Khoa Nhi chịu trách nhiệm chăm sóc sức khỏe cho trẻ sơ sinh và trẻ em. Khoa chuyên điều trị các bệnh thường gặp ở trẻ nhỏ, từ hô hấp đến tim mạch bẩm sinh.'),
('SUR1025', 'Khoa Ngoại', 'Khoa Ngoại đảm nhiệm các ca phẫu thuật và điều trị bằng can thiệp ngoại khoa. Tại đây tiếp nhận các trường hợp cần mổ từ đơn giản đến phức tạp.');

-- --------------------------------------------------------

--
-- Table structure for table `lichkham`
--

CREATE TABLE `lichkham` (
  `maLichKham` varchar(20) NOT NULL,
  `maBenhNhan` varchar(20) DEFAULT NULL,
  `maBacSi` varchar(20) DEFAULT NULL,
  `ngayKham` date NOT NULL,
  `trangThai` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lichkham`
--

INSERT INTO `lichkham` (`maLichKham`, `maBenhNhan`, `maBacSi`, `ngayKham`, `trangThai`) VALUES
('LK1', 'bn1', 'bs1', '2025-11-22', 'pending'),
('LK1762621802637', 'bn1', 'bs1', '2025-11-20', 'cancelled'),
('LK1762621996721', 'bn1', 'bs1', '2025-11-23', 'pending'),
('LK1762622025884', 'bn1', 'bs1', '2025-11-20', 'confirmed'),
('LK1762622033727', 'bn1', 'bs1', '2025-11-21', 'cancelled'),
('LK2', 'bn1', 'bs1', '2025-11-21', 'confirmed'),
('LK20251108181944355', 'bn1', 'bs1', '2025-11-19', 'pending'),
('LK202511081820156', 'bn1', 'bs1', '2025-11-21', 'confirmed'),
('LK202511090024932', 'bn1', 'bs1', '2025-11-20', 'cancelled'),
('LK202511090152952', 'bn1', 'BS202511090152610', '2025-11-08', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `id` int(11) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `soDienThoai` varchar(16) DEFAULT NULL,
  `vaiTro` enum('benhnhan','bacsi','quantri') NOT NULL,
  `trangThai` enum('Hoạt Động','Khóa') NOT NULL DEFAULT 'Hoạt Động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`id`, `tenDangNhap`, `matKhau`, `soDienThoai`, `vaiTro`, `trangThai`) VALUES
(1, 'nguoidung1', 'passwork', '0987654321', 'benhnhan', 'Khóa'),
(2, 'nguoidung2', 'passwork', '0987654321', 'bacsi', 'Hoạt Động'),
(3, 'nguoidung3', 'passwork', '0987654321', 'quantri', 'Hoạt Động'),
(5, 'nguyenthanhccur1025', '$2y$10$j/IqnU9fT0QPeHyZNU1uuum/5IktMdkELYMVs.Uvu9KOgu1PzjXoq', '0917382642', 'bacsi', 'Hoạt Động'),
(7, 'levand', '$2y$10$0w2wLh5q8dn.05WVbEYjc.Epw.C4BLmppiM5Hwj4QO7fbSDvqfOkK', '0361846731', 'bacsi', 'Hoạt Động'),
(8, 'ABCD', '$2y$10$Gucpt7iX418XWZkSgIf8EeSwEj3qDkaepUfrFLc6hiDm.CbmFDqsS', '0936846244', 'benhnhan', 'Khóa'),
(9, 'A1', '$2y$10$LGycidZBlMFFqFVIB5ZTQ.5UZ.ap/TGe1q0BkVr.qKiT726R8st4G', '0111111111', 'benhnhan', 'Hoạt Động');

-- --------------------------------------------------------

--
-- Table structure for table `quantrivien`
--

CREATE TABLE `quantrivien` (
  `nguoiDungId` int(11) NOT NULL,
  `maQuanTriVien` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quantrivien`
--

INSERT INTO `quantrivien` (`nguoiDungId`, `maQuanTriVien`) VALUES
(3, 'admin1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bacsi`
--
ALTER TABLE `bacsi`
  ADD PRIMARY KEY (`maBacSi`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`),
  ADD KEY `maChuyenKhoa` (`maChuyenKhoa`);

--
-- Indexes for table `benhnhan`
--
ALTER TABLE `benhnhan`
  ADD PRIMARY KEY (`maBenhNhan`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`);

--
-- Indexes for table `chuyenkhoa`
--
ALTER TABLE `chuyenkhoa`
  ADD PRIMARY KEY (`maChuyenKhoa`),
  ADD KEY `maKhoa` (`maKhoa`);

--
-- Indexes for table `hosobenhan`
--
ALTER TABLE `hosobenhan`
  ADD PRIMARY KEY (`maHoSo`),
  ADD KEY `maBenhNhan` (`maBenhNhan`),
  ADD KEY `maBacSi` (`maBacSi`);

--
-- Indexes for table `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`maKhoa`);

--
-- Indexes for table `lichkham`
--
ALTER TABLE `lichkham`
  ADD PRIMARY KEY (`maLichKham`),
  ADD KEY `maBenhNhan` (`maBenhNhan`),
  ADD KEY `maBacSi` (`maBacSi`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenDangNhap` (`tenDangNhap`);

--
-- Indexes for table `quantrivien`
--
ALTER TABLE `quantrivien`
  ADD PRIMARY KEY (`maQuanTriVien`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bacsi`
--
ALTER TABLE `bacsi`
  ADD CONSTRAINT `bacsi_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bacsi_ibfk_2` FOREIGN KEY (`maChuyenKhoa`) REFERENCES `chuyenkhoa` (`maChuyenKhoa`) ON DELETE SET NULL;

--
-- Constraints for table `benhnhan`
--
ALTER TABLE `benhnhan`
  ADD CONSTRAINT `benhnhan_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chuyenkhoa`
--
ALTER TABLE `chuyenkhoa`
  ADD CONSTRAINT `chuyenkhoa_ibfk_1` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`) ON DELETE CASCADE;

--
-- Constraints for table `hosobenhan`
--
ALTER TABLE `hosobenhan`
  ADD CONSTRAINT `hosobenhan_ibfk_1` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`) ON DELETE CASCADE,
  ADD CONSTRAINT `hosobenhan_ibfk_2` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE SET NULL;

--
-- Constraints for table `lichkham`
--
ALTER TABLE `lichkham`
  ADD CONSTRAINT `lichkham_ibfk_1` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`) ON DELETE SET NULL,
  ADD CONSTRAINT `lichkham_ibfk_2` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE SET NULL;

--
-- Constraints for table `quantrivien`
--
ALTER TABLE `quantrivien`
  ADD CONSTRAINT `quantrivien_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE;
COMMIT;