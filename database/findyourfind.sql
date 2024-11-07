-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2024 at 11:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `findyourfind`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductOrderDetails` (IN `in_product_id` INT)   BEGIN
    DECLARE total_orders INT DEFAULT 0;      -- Stores the total number of orders
    DECLARE total_qty INT DEFAULT 0;         -- Stores the total quantity ordered
    DECLARE prod_name VARCHAR(255);
    DECLARE category_name VARCHAR(255);
    DECLARE order_id INT;
    DECLARE json_order_details JSON;
    DECLARE json_length INT;
    DECLARE i INT DEFAULT 0;
    DECLARE qty INT;
    DECLARE done INT DEFAULT 0;  
    DECLARE exit_inner_loop BOOLEAN DEFAULT FALSE;  -- Flag to control inner loop exit

    -- Cursor to iterate over order ids
    DECLARE order_cursor CURSOR FOR 
        SELECT order_id, order_details FROM tbl_orders;

    -- Handler for when the cursor reaches the end of data
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Get product name and category
    SELECT p.pro_name, c.category
    INTO prod_name, category_name
    FROM products p
    JOIN product_category c ON p.category_id = c.category_id
    WHERE p.id = in_product_id;

    -- Open the cursor
    OPEN order_cursor;

    -- Cursor loop
    order_loop: LOOP
        FETCH order_cursor INTO order_id, json_order_details;
        
        -- End loop if there are no more rows
        IF done THEN 
            LEAVE order_loop;
        END IF;

        -- Get the length of JSON array
        SET json_length = JSON_LENGTH(json_order_details);

        -- Reset exit_inner_loop flag
        SET exit_inner_loop = FALSE;

        -- Loop over JSON array elements
        SET i = 0;
        WHILE i < json_length AND exit_inner_loop = FALSE DO
            -- Check if product_id matches
            IF JSON_UNQUOTE(JSON_EXTRACT(json_order_details, CONCAT('$[', i, '].product_id'))) = in_product_id THEN
                -- Increment total_qty by qty
                SET qty = JSON_UNQUOTE(JSON_EXTRACT(json_order_details, CONCAT('$[', i, '].qty')));
                SET total_qty = total_qty + qty;

                -- Increment total_orders by 1 for each order containing the product
                SET total_orders = total_orders + 1;
                
                -- Set exit_inner_loop to true to exit the WHILE loop
                SET exit_inner_loop = TRUE;
            END IF;

            SET i = i + 1;
        END WHILE;
    END LOOP;

    CLOSE order_cursor;

    -- Output results
    SELECT prod_name AS product_name, category_name AS category, total_orders, total_qty;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_logs`
--

CREATE TABLE `order_logs` (
  `log_id` int(11) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_logs`
--

INSERT INTO `order_logs` (`log_id`, `order_id`, `order_quantity`, `timestamp`) VALUES
(1, 9, 8, '2024-10-27 16:16:35'),
(2, 10, 3, '2024-11-05 06:50:04'),
(3, 11, 4, '2024-11-05 07:37:16'),
(4, 12, 3, '2024-11-07 09:15:24');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) NOT NULL,
  `pro_name` varchar(255) DEFAULT NULL,
  `pro_rp` double DEFAULT NULL,
  `pro_sp` double DEFAULT NULL,
  `pro_desc` longtext DEFAULT NULL,
  `available` int(11) DEFAULT 1,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `pro_name`, `pro_rp`, `pro_sp`, `pro_desc`, `available`, `category_id`) VALUES
(1, 'Iphone 15 pro', 1098, 999, 'As part of our efforts to reach carbon neutrality by 2030, iPhone 15 Pro and iPhone 15 Pro Max do not include a power adapter or EarPods. Included in the box is a USB-C Charge Cable that supports fast charging and is compatible with USB-C power adapters and computer ports. We encourage you to use any compatible USB-C power adapter. If you need a new Apple power adapter or headphones, they are available for purchase.\r\n                    \r\n                    \r\n                    ', 6, 1),
(2, 'Gucci Wallet', 699, 498, 'Gucci products are made with carefully selected materials. Please handle with care for longer product life. Protect from direct light, heat and rain. Should it become wet, dry it immediately with a soft cloth Store in the provided flannel bag or box Clean with a soft, dry cloth', 1, 10),
(3, 'Nike Air Jordan 1', 95, 78, 'Inspired by the original AJ1, this mid-top edition maintains the iconic look you love while choice colours and crisp leather give it a distinct identity.', 6, NULL),
(4, 'One8 by Virat Kohli Tshirt', 12, 10, 'Knitted pure cotton fabric Graphic printed Regular length Round neck Short regular sleeves', 5, NULL),
(5, 'Rolex Watch', 3599, 3199, 'Launched in 1953, the Rolex Submariner is the first divers wristwatch to be waterproof to a depth of 100 metres (330 feet) now 300 metres (1,000 feet). Its major features, such as the graduated rotatable bezel, the luminescent display, the large hands and hour markers, have been a driving force in the creation of the long line of Rolex divers watches which followed. The Submariner is an iconic timepiece whose renown now extends beyond the professional world it was first designed for. The Submariner, the ultimate standard.', 0, 10),
(6, 'boAt Stone 750 Portable Bluetooth Speaker (12W) ', 80, 45, 'The Boat Stone 750 12W BT speaker is a portable Bluetooth speaker designed for outdoor enthusiasts. With a powerful 12W output, it delivers crisp sound and enhanced bass, making it perfect for music lovers. Its rugged build features an IPX7 rating, ensuring resistance to water and dust, ideal for poolside or beach use. The speaker offers up to 10 hours of playback on a single charge, allowing for extended enjoyment. Lightweight and equipped with a convenient strap, it’s easy to carry, making it a great companion for adventures.', 0, 1),
(7, 'CR7 Lava Hypervenom Magista', 350, 300, 'The CR7 Lava Hypervenom Magista collection combines cutting-edge design with inspiration from volcanic energy, reflecting Cristiano Ronaldo\'s fiery passion and explosive speed on the field. The boots feature a unique lava-like color gradient of fiery reds and deep blacks, representing the intense force of molten lava. Crafted with Nike’s advanced Flyknit technology, these boots ensure a snug, sock-like fit that enhances control and agility. A textured upper optimizes ball touch, while the split-toe soleplate and strategically placed studs provide explosive traction for sharp cuts and rapid acceleration. Designed for elite performance, the Lava Hypervenom Magista is perfect for players who play with relentless speed and precision, much like CR7 himself.', 12, 7),
(8, 'Samsung 8.0 kg AI Ecobubble Front Load Washing Machine', 485, 440, 'The 8.0 kg AI Ecobubble Front Load Washing Machine combines intelligent AI-powered washing with efficient EcoBubble technology, ensuring powerful stain removal even at low temperatures. It customizes washing cycles based on fabric types and load size, providing optimal care for your clothes. With its eco-friendly design, it reduces water and energy consumption, making it ideal for sustainable households. The machine features a sleek, modern look, a large LED display, and a quiet operation mode for convenient, stress-free laundry. Perfect for families, it balances innovation and performance for a superior clean every wash.', 3, 8),
(9, 'Yamaha F280 Acoustic Guitar - Natural', 110, 90, 'The Yamaha F280 Acoustic Guitar in Natural finish offers a rich and resonant sound, making it an ideal choice for both beginners and experienced players. Its classic dreadnought body shape ensures excellent projection and a full tonal range, while the solid spruce top paired with meranti back and sides enhances durability and sound quality. The guitar features a comfortable neck design for easy playability and is beautifully crafted with a natural gloss finish that highlights its elegant grain. Whether you\'re strumming chords or picking melodies, the Yamaha F280 delivers an inspiring musical experience.', 6, 9),
(10, 'Harry Potter and the Philosophers Stone', 8, 6, 'Harry Potter and the Philosopher\\\'s Stone is the first novel in J.K. Rowling\\\'s beloved Harry Potter series. The story introduces us to Harry, an ordinary boy who learns on his eleventh birthday that he is actually a wizard. Taken from a mundane life with his cruel aunt and uncle, he discovers a magical world filled with wonder, mystery, and danger. At Hogwarts School of Witchcraft and Wizardry, he makes friends like Ron Weasley and Hermione Granger and encounters the dark magic of the powerful, malevolent wizard Voldemort. The book follows Harry\\\'s journey as he learns about his past, his parents\\\' legacy, and the legendary Philosopher\\\'s Stone, an object with the power to grant immortality.', 18, 4),
(11, 'Kookaburra Bat with Tennis Ball', 150, 115, 'Best cricket bat advanced players. Khelo aur khelne do!', 5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `category_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`category_id`, `category`) VALUES
(6, 'Beauty & Health'),
(4, 'Books'),
(1, 'Electronics'),
(2, 'Furniture'),
(5, 'Groceries'),
(8, 'Home Appliances'),
(9, 'Musical Instruments'),
(10, 'Others'),
(7, 'Sports & Outdoors');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_address`
--

CREATE TABLE `tbl_address` (
  `address_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `address` varchar(200) NOT NULL,
  `zip` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_address`
--

INSERT INTO `tbl_address` (`address_id`, `customer_id`, `address`, `zip`, `state`, `city`) VALUES
(2, 31, 'test 1w3213', 'test', 'test', 'test'),
(3, 34, 'basfvqd', '244001', 'Kothiwal nagar, budh bazar', 'Moradabad'),
(4, 36, '123 Maple Street\r\nSpringfield, IL 62704\r\nUSA', '62704', 'Nevada', 'Springfield'),
(5, 37, 'Flat No. 12, Silver Oak Apartments, 5th Floor, Lokhandwala Complex, Andheri West, Mumbai, Maharashtra 400053, India', '400053', 'Maharashtra', 'Mumbai'),
(6, 38, '45 Rue Al Mouqawama\r\nQuartier Gauthier\r\nCasablanca 20000\r\nMorocco', '586234', 'Casablanca', 'Quartier Gauthier');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`admin_id`, `admin_email`, `admin_password`) VALUES
(1, 'admin@gmail.com', 'e6e061838856bf47e1de730719fb2609');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cart`
--

CREATE TABLE `tbl_cart` (
  `cart_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `order_id` bigint(20) NOT NULL,
  `title` varchar(500) NOT NULL,
  `order_details` text NOT NULL,
  `total_amount` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `order_status` bigint(20) NOT NULL,
  `order_date` date NOT NULL,
  `order_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_orders`
--

INSERT INTO `tbl_orders` (`order_id`, `title`, `order_details`, `total_amount`, `user_id`, `order_status`, `order_date`, `order_quantity`) VALUES
(3, 'Bulova Watch 1 498 ( 1 * 498 ) = 498<br>Bulova Watch 1 498 ( 1 * 498 ) = 498<br>Bulova Watch 1 498 ( 1 * 498 ) = 498<br>', '[{\"cart_id\":\"19\",\"product_id\":\"1\",\"user_id\":\"31\",\"qty\":\"1\",\"id\":\"1\",\"pro_name\":\"Bulova Watch\",\"pro_rp\":\"699\",\"pro_sp\":\"498\",\"pro_desc\":\"\",\"pro_img_1\":\"c64fbffae5d715fca7c7646a78d83e84.png\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"},{\"cart_id\":\"20\",\"product_id\":\"2\",\"user_id\":\"31\",\"qty\":\"1\",\"id\":\"2\",\"pro_name\":\"Bulova Watch\",\"pro_rp\":\"699\",\"pro_sp\":\"498\",\"pro_desc\":\"\",\"pro_img_1\":\"c64fbffae5d715fca7c7646a78d83e84.png\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"},{\"cart_id\":\"21\",\"product_id\":\"3\",\"user_id\":\"31\",\"qty\":\"1\",\"id\":\"3\",\"pro_name\":\"Bulova Watch\",\"pro_rp\":\"699\",\"pro_sp\":\"498\",\"pro_desc\":\"\",\"pro_img_1\":\"c64fbffae5d715fca7c7646a78d83e84.png\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"}]', '1494', 31, 1, '2024-04-03', 3),
(4, 'Bulova Watch 1 498 ( 1 * 498 ) = 498<br>Bulova Watch 1 498 ( 1 * 498 ) = 498<br>', '[{\"cart_id\":22,\"product_id\":2,\"user_id\":34,\"qty\":1,\"id\":2,\"pro_name\":\"Bulova Watch\",\"pro_rp\":699,\"pro_sp\":498,\"pro_desc\":\"\",\"pro_img_1\":\"\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"},{\"cart_id\":23,\"product_id\":1,\"user_id\":34,\"qty\":1,\"id\":1,\"pro_name\":\"Bulova Watch\",\"pro_rp\":699,\"pro_sp\":498,\"pro_desc\":\"\",\"pro_img_1\":\"\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"}]', '996', 34, 1, '2024-06-10', 2),
(5, 'Iphone 15 pro 1 999 ( 1 * 999 ) = 999<br>', '[{\"cart_id\":24,\"product_id\":1,\"user_id\":34,\"qty\":1,\"id\":1,\"pro_name\":\"Iphone 15 pro\",\"pro_rp\":1098,\"pro_sp\":999,\"pro_desc\":\"\",\"pro_img_1\":\"9850bc10bba2ba3abfe13420129cd638.jpeg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"}]', '999', 34, 1, '2024-10-15', 1),
(6, 'Iphone 15 pro 2 999 ( 2 * 999 ) = 1998<br>One8 by Virat Kohli Tshirt 1 10 ( 1 * 10 ) = 10<br>', '[{\"cart_id\":25,\"product_id\":1,\"user_id\":36,\"qty\":2,\"id\":1,\"pro_name\":\"Iphone 15 pro\",\"pro_rp\":1098,\"pro_sp\":999,\"pro_desc\":\"\",\"pro_img_1\":\"9850bc10bba2ba3abfe13420129cd638.jpeg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"},{\"cart_id\":26,\"product_id\":4,\"user_id\":36,\"qty\":1,\"id\":4,\"pro_name\":\"One8 by Virat Kohli Tshirt\",\"pro_rp\":12,\"pro_sp\":10,\"pro_desc\":\"\",\"pro_img_1\":\"c4f05c835a5624fe2c4d82ba45547b3a.jpg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\"}]', '2008', 36, 1, '2024-10-19', 3),
(7, 'Iphone 15 pro 3 999 ( 3 * 999 ) = 2997<br>', '[{\"cart_id\":33,\"product_id\":1,\"user_id\":37,\"qty\":3,\"id\":1,\"pro_name\":\"Iphone 15 pro\",\"pro_rp\":1098,\"pro_sp\":999,\"pro_desc\":\"\",\"pro_img_1\":\"9850bc10bba2ba3abfe13420129cd638.jpeg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\",\"available\":5}]', '2997', 37, 1, '2024-10-25', 3),
(8, 'One8 by Virat Kohli Tshirt 1 10 ( 1 * 10 ) = 10<br>Nike Air Jordan 1 1 78 ( 1 * 78 ) = 78<br>CR7 Lava Hypervenom Magista 1 300 ( 1 * 300 ) = 300<br>', '[{\"cart_id\":31,\"product_id\":4,\"user_id\":36,\"qty\":1,\"id\":4,\"pro_name\":\"One8 by Virat Kohli Tshirt\",\"pro_rp\":12,\"pro_sp\":10,\"pro_desc\":\"\",\"pro_img_1\":\"c4f05c835a5624fe2c4d82ba45547b3a.jpg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\",\"available\":1},{\"cart_id\":34,\"product_id\":3,\"user_id\":36,\"qty\":1,\"id\":3,\"pro_name\":\"Nike Air Jordan 1\",\"pro_rp\":97,\"pro_sp\":78,\"pro_desc\":\"\",\"pro_img_1\":\"22cb422d41dc9e5d2769f2c0d4c4a3d1.jpeg\",\"pro_img_2\":\"ee4bc8fe3a6b3064d2ff5535178ab730.jpg\",\"pro_img_3\":\"1e26ff3f008920f749095ef8e4ae8214.jpg\",\"available\":20},{\"cart_id\":35,\"product_id\":7,\"user_id\":36,\"qty\":1,\"id\":7,\"pro_name\":\"CR7 Lava Hypervenom Magista\",\"pro_rp\":350,\"pro_sp\":300,\"pro_desc\":\"\",\"pro_img_1\":null,\"pro_img_2\":null,\"pro_img_3\":null,\"available\":15}]', '388', 36, 1, '2024-10-25', 3),
(9, 'Nike Air Jordan 1 2 78 ( 2 * 78 ) = 156<br>One8 by Virat Kohli Tshirt 3 10 ( 3 * 10 ) = 30<br>Rolex Watch 1 3199 ( 1 * 3199 ) = 3199<br>CR7 Lava Hypervenom Magista 1 300 ( 1 * 300 ) = 300<br>Samsung 8.0 kg AI Ecobubble Front Load Washing Machine 1 440 ( 1', '[{\"cart_id\":36,\"product_id\":3,\"user_id\":36,\"qty\":2,\"id\":3,\"pro_name\":\"Nike Air Jordan 1\",\"pro_rp\":97,\"pro_sp\":78,\"pro_desc\":\"\",\"available\":19,\"category_id\":3},{\"cart_id\":37,\"product_id\":4,\"user_id\":36,\"qty\":3,\"id\":4,\"pro_name\":\"One8 by Virat Kohli Tshirt\",\"pro_rp\":12,\"pro_sp\":10,\"pro_desc\":\"\",\"available\":12,\"category_id\":3},{\"cart_id\":38,\"product_id\":5,\"user_id\":36,\"qty\":1,\"id\":5,\"pro_name\":\"Rolex Watch\",\"pro_rp\":3599,\"pro_sp\":3199,\"pro_desc\":\"\",\"available\":1,\"category_id\":10},{\"cart_id\":39,\"product_id\":7,\"user_id\":36,\"qty\":1,\"id\":7,\"pro_name\":\"CR7 Lava Hypervenom Magista\",\"pro_rp\":350,\"pro_sp\":300,\"pro_desc\":\"\",\"available\":14,\"category_id\":3},{\"cart_id\":40,\"product_id\":8,\"user_id\":36,\"qty\":1,\"id\":8,\"pro_name\":\"Samsung 8.0 kg AI Ecobubble Front Load Washing Machine\",\"pro_rp\":485,\"pro_sp\":440,\"pro_desc\":\"\",\"available\":7,\"category_id\":8}]', '4125', 36, 1, '2024-10-27', 8),
(10, 'Yamaha F280 Acoustic Guitar - Natural 2 90 ( 2 * 90 ) = 180<br>CR7 Lava Hypervenom Magista 1 300 ( 1 * 300 ) = 300<br>', '[{\"cart_id\":43,\"product_id\":9,\"user_id\":36,\"qty\":2,\"id\":9,\"pro_name\":\"Yamaha F280 Acoustic Guitar - Natural\",\"pro_rp\":110,\"pro_sp\":90,\"pro_desc\":\"\",\"available\":8,\"category_id\":9},{\"cart_id\":44,\"product_id\":7,\"user_id\":36,\"qty\":1,\"id\":7,\"pro_name\":\"CR7 Lava Hypervenom Magista\",\"pro_rp\":350,\"pro_sp\":300,\"pro_desc\":\"\",\"available\":13,\"category_id\":3}]', '480', 36, 1, '2024-11-05', 3),
(11, 'Harry Potter and the Philosophers Stone 1 6 ( 1 * 6 ) = 6<br>Nike Air Jordan 1 2 78 ( 2 * 78 ) = 156<br>One8 by Virat Kohli Tshirt 1 10 ( 1 * 10 ) = 10<br>', '[{\"cart_id\":45,\"product_id\":10,\"user_id\":38,\"qty\":1,\"id\":10,\"pro_name\":\"Harry Potter and the Philosophers Stone\",\"pro_rp\":8,\"pro_sp\":6,\"pro_desc\":\"\",\"available\":19,\"category_id\":4},{\"cart_id\":46,\"product_id\":3,\"user_id\":38,\"qty\":2,\"id\":3,\"pro_name\":\"Nike Air Jordan 1\",\"pro_rp\":97,\"pro_sp\":78,\"pro_desc\":\"\",\"available\":11,\"category_id\":3},{\"cart_id\":47,\"product_id\":4,\"user_id\":38,\"qty\":1,\"id\":4,\"pro_name\":\"One8 by Virat Kohli Tshirt\",\"pro_rp\":12,\"pro_sp\":10,\"pro_desc\":\"\",\"available\":6,\"category_id\":3}]', '172', 38, 1, '2024-11-05', 4),
(12, 'Nike Air Jordan 1 3 78 ( 3 * 78 ) = 234<br>', '[{\"cart_id\":49,\"product_id\":3,\"user_id\":36,\"qty\":3,\"id\":3,\"pro_name\":\"Nike Air Jordan 1\",\"pro_rp\":95,\"pro_sp\":78,\"pro_desc\":\"\",\"available\":9,\"category_id\":3}]', '234', 36, 1, '2024-11-07', 3);

--
-- Triggers `tbl_orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `tbl_orders` FOR EACH ROW BEGIN
    INSERT INTO order_logs (order_id, order_quantity)
    VALUES (NEW.order_id, NEW.order_quantity);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_status`
--

CREATE TABLE `tbl_order_status` (
  `status_id` bigint(20) NOT NULL,
  `status_text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_order_status`
--

INSERT INTO `tbl_order_status` (`status_id`, `status_text`) VALUES
(1, 'Created'),
(2, 'Processing'),
(3, 'Shipped'),
(4, 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_images`
--

CREATE TABLE `tbl_product_images` (
  `img_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `img_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product_images`
--

INSERT INTO `tbl_product_images` (`img_id`, `product_id`, `img_path`) VALUES
(1, 1, '9850bc10bba2ba3abfe13420129cd638.jpeg'),
(2, 2, 'bc7582481c0d1d2bedf261b5666e3df8.jpg'),
(3, 3, '22cb422d41dc9e5d2769f2c0d4c4a3d1.jpeg'),
(4, 4, 'c4f05c835a5624fe2c4d82ba45547b3a.jpg'),
(5, 5, '1554fdfd6604a97b3e469c59d7fa62e2.jpg'),
(6, 6, 'boatStone.webp'),
(7, 7, 'cr7_hypervenom.jpg'),
(8, 8, 'wash_mach.webp'),
(9, 9, '61b6DTHgNWL.jpg'),
(10, 10, '9781408855652.jpg'),
(11, 11, 'f16a2b10a8e2e86237345bad62d32e8c.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_has_wishlist`
--

CREATE TABLE `tbl_user_has_wishlist` (
  `wishlist_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_has_wishlist`
--

INSERT INTO `tbl_user_has_wishlist` (`wishlist_id`, `user_id`, `product_id`) VALUES
(12, 31, 3),
(13, 31, 1),
(19, 36, 1),
(21, 38, 1),
(22, 38, 2),
(23, 36, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `user_name` text DEFAULT NULL,
  `user_email` text DEFAULT NULL,
  `user_mobile` text DEFAULT NULL,
  `user_password` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `user_email`, `user_mobile`, `user_password`) VALUES
(26, 'Abhay Kakkar', 'abhaykakkar@gmail.com', ' 9876543210', '70efdf2ec9b086079795c442636b55fb'),
(27, 'Vansh ', 'v.kakkar1994@gmail.com', ' 7505791617', '81dc9bdb52d04dc20036dbd8313ed055'),
(29, 'Pranav Kumar Gupta', 'pkg3107@gmail.com', ' 9013873573', '209a789122b86d6e77f295ca2a264ab9'),
(31, 'govind', 'govindarchive@gmail.com', ' 7877190812', 'c3391086d5965242044b21fd89f162ee'),
(32, '1234', '1234@1234.com', ' 12345678', '81dc9bdb52d04dc20036dbd8313ed055'),
(33, 'abcd', 'abcd@gmail.com', ' 1234567890', 'e19d5cd5af0378da05f63f891c7467af'),
(34, 'Ansh Gagneja', 'anshgagneja1614@gmail.com', ' 8171786800', '4dc113ceb964e24d0dd8d58306f9b126'),
(35, 'ansh', 'ansh@gmail.com', '9876543210', 'a12b61025c1f493a8ad8d4dcf6630883'),
(36, 'Ronnie Gupta', 'ronnie@gmail.com', ' 8635652594', '6c6df59fc9a6c8523031238265cba829'),
(37, 'Rohit Sharma', 'rohit@bcci.in', ' 8695658412', '99d0bfcce1e9b31caf53c4ea0853decd'),
(38, 'Ishan Mrinal', 'ishan654@yahoo.com', ' 6352915463', '13b2ab89cfc2f9975165d45cbab32b90');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pro_name` (`pro_name`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category` (`category`);

--
-- Indexes for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_customer` (`customer_id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_customer_cart` (`user_id`),
  ADD KEY `fk_product_cart` (`product_id`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_customer_order` (`user_id`),
  ADD KEY `fk_order_status` (`order_status`);

--
-- Indexes for table `tbl_order_status`
--
ALTER TABLE `tbl_order_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `tbl_product_images`
--
ALTER TABLE `tbl_product_images`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `fk_image` (`product_id`);

--
-- Indexes for table `tbl_user_has_wishlist`
--
ALTER TABLE `tbl_user_has_wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `fk_customer_wishlist` (`user_id`),
  ADD KEY `fk_product_wishlist` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_address`
--
ALTER TABLE `tbl_address`
  MODIFY `address_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  MODIFY `cart_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `order_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_order_status`
--
ALTER TABLE `tbl_order_status`
  MODIFY `status_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_product_images`
--
ALTER TABLE `tbl_product_images`
  MODIFY `img_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_user_has_wishlist`
--
ALTER TABLE `tbl_user_has_wishlist`
  MODIFY `wishlist_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `order_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `product_category` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD CONSTRAINT `fk_customer_cart` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_product_cart` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD CONSTRAINT `fk_customer_order` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_order_status` FOREIGN KEY (`order_status`) REFERENCES `tbl_order_status` (`status_id`);

--
-- Constraints for table `tbl_product_images`
--
ALTER TABLE `tbl_product_images`
  ADD CONSTRAINT `fk_image` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `tbl_user_has_wishlist`
--
ALTER TABLE `tbl_user_has_wishlist`
  ADD CONSTRAINT `fk_customer_wishlist` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_product_wishlist` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
