<?php
$user_session_id = uniqid();
// session_id($user_session_id);
session_start();
$_SESSION['user_id'] = $user_session_id;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Безупречная Чебуречная</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue&display=swap" rel="stylesheet">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@100;300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>


<?php
require_once 'config/databases.php';
$sql = "SELECT * FROM `MenuItems` WHERE 1=1";
$result = mysqli_query($induction, $sql);

$sqlcategories = "SELECT * FROM `Categories` WHERE 1=1";
$resultcategories = mysqli_query($induction, $sqlcategories);
?>



<body>

    <header class="header">

        <a href="" class="logo">
            <img src="images/zagruzheno.png" alt="">
        </a>

        <nav class="navbar">
            <a href="#home">Главная</a>
            <a href="#about">О нас</a>
            <a href="#menu">Меню</a>
            <a href="#review">отзывы</a>
            <a href="#contact">Находимся</a>
            <a href="#contact">Соц. сети</a>
        </nav>

        <div class="icons">
            <div class="fas fa-search" id="search-btn"></div>
            <div class="fas fa-shopping-cart" id="cart-btn">
                <span id="cart-count">0</span>
            </div>
            <div class="fas fa-bars" id="menu-btn"></div>
        </div>

        <div class="search-form">
            <input type="search" id="search-box" placeholder="Искать...">
            <label for="search-box" class="fas fa-search"></label>
        </div>

        <div class="cart-items-container">
    <a class="btn" id="show-cart-btn">Показать содержимое корзины</a>
    <div class="modal" id="cart-modal">
    <div class="modal-content">
        <div id="cart-items-list">
            <!-- Значения списка товаров в корзине будут добавлены динамически -->
        </div>
        <a href="../config/cart_page.php" class="btn" id="goto-order-page-btn">Перейти к оформлению</a>
    </div>
</div>

    </header>

    <section class="home" id="home">
        <div class="content">
            <div class="images">
                <img src="images/zagruzheno.png" alt="">
            </div>
            <a href="#menu" class="btn">Меню</a>
        </div>
    </section>

    <section class="about" id="about">

        <h1 class="heading">О <span>Нас</span></h1>

        <div class="row">
            <div class="image">
                <img src="images/cheb1.jpg" alt="">
            </div>

            <div class="content">
                <h3>БЕЗУПРЕЧНАЯ ЧЕБУРЕЧНАЯ</h3>
                <p>Наше кафе находится в самом сердце нашего города. Недалеко от нас прекрасная набережная, парки, море.
                    Поэтому после прекрасной прогулки ждем вас к нам на завтрак-обед-ужин.</p>
                <p>Очень вкусно, по домашнему, и быстро.</p>
                <p>-Чебуреки от 135р</p>
                <p>-Завтрак 199р</p>
                <p>-Комплексный обед 299р</p>
                <p>-Разнообразное меню</p>
                <p>Так же действуют различные акции, скорее приходи мы тебя ждем!</p>
                <a href="#menu" class="btn">Наше меню</a>
            </div>
        </div>
    </section>


    <section class="menu" id="menu">
        <h1 class="heading"><span>Меню</span></h1>

        <div class="menu-sorting-btns box-container">
            <button class="sorting-btn" data-category="all">Все</button>

            <?php while ($rowcategories = mysqli_fetch_assoc($resultcategories)): ?>
                <button class="sorting-btn" data-category="<?= $rowcategories['category_id'] ?>">
                    <?= $rowcategories['category_name'] ?>
                </button>
            <?php endwhile; ?>
        </div>

        <div class="box-container">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="box" data-item-id="<?= $row['item_id'] ?>" data-category="<?= $row['category_id'] ?>">
                    <img style="height: 22rem; border-radius: 10px;" src="<?= $row['image_url'] ?>" alt="">
                    <h3>
                        <?= $row['item_name'] ?>
                    </h3>
                    <div class="price">
                        <?= $row['price']?>₽
                        <a href="#" class="btn buy-btn" data-item-id="<?= $row['item_id'] ?>">Добавить в корзину</a>
                    </div>

                    <?php if ($_SESSION["admink"]) { ?>
                        <a href="#" class="btn edit-btn" data-item-id="<?= $row['item_id'] ?>">Изменить</a>
                        <a href="/config/delete.php?id=<?= $row['item_id'] ?>" class="btn">Удалить</a>
                    <?php } ?>
                </div>
            <?php endwhile; ?>
            <?php if ($_SESSION["admink"]) { ?>
                <div class="box">
                    <img src="images/menu-1.jpg" alt="">
                    <h3>Новый товар</h3>
                    <div class="price"></div>
                    <a href="/config/addempty.php" class="btn">Добавить</a>
                </div>
            <?php } ?>

        </div>
    </section>

    <!-- menu end-->

    <!-- HTML модального окна -->
    <div id="openModal" class="modalDialog">
        <div class="modal-content">
            <a href="#close" title="Закрыть" class="close">X</a>
            <h2>Редактировать товар</h2>
            <form id="editForm" enctype="multipart/form-data">
                <input type="hidden" id="itemId" name="itemId">

                <input type="file" id="itemImageInput" name="itemImage" accept="image/*">
                <img id="itemImage" src="" alt="">
                <p>НОВОЕ ИЗОБРАЖЕНИЕ В СПИСКЕ ТОВАРОВ ПОЯВИТСЯ ТОЛЬКО ПОСЛЕ ПЕРЕЗАГРУЗКИ СТРАНИЦЫ </p>

                <div class="input-container">
                    <label for="itemName">Название товара:</label>
                    <input type="text" id="itemName" name="itemName" required>
                </div>
                <div class="input-container">
                    <label for="itemPrice">Цена:</label>
                    <input type="text" id="itemPrice" name="itemPrice" required>
                </div>
                <div class="input-container">
                    <label for="categorySelect">Категория:</label>
                    <select id="categorySelect" name="categoryId"></select>
                </div>
                <div id="categoryError" class="error-message"></div>
                <button type="button" id="editSubmit">Сохранить изменения</button>

            </form>
        </div>
    </div>


    <section class="review" id="review">

        <h1 class="heading">О<span>тзывы</span></h1>

        <div class="box-container">
            <div class="box">
                <i class="fa-solid fa-quote-left"></i>
                <p></p>
                <img src="images/user2.jpg" class="user" alt="">
                <h3>Надежда Иванченко</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Очень вкусно и довольно не дорого для практически центра Севастополя. Хорошее и быстрое обслуживание.
                    В меню не только сухомятка в виде чебуреков и разного рода фастфуда, но и супчики, что очень
                    понравилось ребёнку! Однозначно порекомендую комплексные обеды (бизнес ланч)!</p>
            </div>
            <div class="box" >
                <i class="fa-solid fa-quote-left"></i>
                <p></p>
                <img src="images/user3.jpg" class="user" alt="">
                <h3>Ирина</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Хожу в чебуречную на обед. Как не парадоксально, но в чебуречной шикарный борщ!
                    Сытный, вкусный... Мммм... Спасибо, что не оставляете голодными. Ну, и цены очень радуют. Для меня
                    обед - борщ и салат - обходятся в 179 руб.. Ну, сказка?</p>
            </div>
            <div class="box">
                <i class="fa-solid fa-quote-left"></i>
                <p></p>
                <img src="images/user1.jpg" class="user" alt="">
                <h3>Евгений</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Приятно сюда приходить, то что чебуреки очень вкусные это одно, но обслуживание, какое редко
                    встретишь, приятные девочки, очень стараются, всегда улыбаются. Атмосфера решает многое, вот и хожу
                    сюда постоянно. Девочкам респект Диана и Николь</p>
            </div>
        </div>
    </section>


    <section class="contact" id="contact">
        <h1 class="heading"><span>МЫ</span> НАХОДИМСЯ</h1>

        <div class="row">

            <iframe class="map"
                src="https://yandex.ru/map-widget/v1/?um=constructor%3Abaf7a71ef2837d50981722010bc65959c1d9e9eeba22331bb75bf37a5f8947a2&amp;source=constructor"
                allowfullscreen="" loading="lazy"></iframe>

            <form name='submit-to-google-sheet'>
                <h3>Забронировать зал</h3>
                <span id='msg'></span>
                <div class="inputBox">
                    <span class="fas fa-user"></span>
                    <input type="text" name='text' placeholder="Имя">
                </div>
                <div class="inputBox">
                    <span class="fas fa-envelope"></span>
                    <input type="email" name='email' placeholder="Сообщение">
                </div>
                <div class="inputBox">
                    <span class="fas fa-phone"></span>
                    <input type="number" name='number' placeholder="телефон">
                </div>
                <input type="submit" value="отправить" class="btn">

            </form>

        </div>

    </section>
    <!-- footer start-->
    <section class="footer">
        <div class="share">
            <a href="https://vk.com/cheburek_sevas" class="fa-brands fa-vk"></a>
            <a href="https://instagram.com/_bezuprechnaya_cheburechnay_?igshid=NTc4MTIwNjQ2YQ=="
                class="fa-brands fa-instagram""></a>
            <a href=" https://ok.ru/group/70000002619640" class="fa-brands fa-odnoklassniki"></a>
            <a href="https://t.me/bezuprechnaya_cheburechnay" class="fa-brands fa-telegram"></a>
        </div>
        <div class="links">
            <a href="#home">Главная</a>
            <a href="#about">О нас</a>
            <a href="#menu">Меню</a>
            <a href="#review">отзывы</a>
            <a href="#contact">Находимся</a>
            <a href="#contact">Соц. сети</a>
            <a href="/config/account.php">Администрирование</a>
        </div>
        <div class="credit">Наши Контакты: <span>+7 (978) 250-47-35</span></div>
    </section>
    <!-- footer end-->
    <footer class="site-footer">
        <p>Разработано компанией <a style="color:white; text-decoration: underline;"
                href="http://rococo-chimera-8f02ff.netlify.app/">ZiM™</a></p>
        <p> &copy; 2023 - Все права защищены </p>
    </footer>
    <style>
        .site-footer {
            width: 100%;
            text-align: center;
            padding: 20px;
            background: #711520;
            color: white;
            text-decoration:none; 
            font-size: 150%;
        }
    </style>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/script.js"></script>

    <script>
        $(document).ready(function () {
            // Ваш код, использующий jQuery
        });
        
        const scriptURL = 'https://script.google.com/macros/s/AKfycbzzLL9ZT1wBRahWSYkrXfRLSrtB8_B2AgiVCilIZrX1hUMt4GUrj8Tom_L2vHGkyx7w6A/exec'
        const form = document.forms['submit-to-google-sheet']
        const msg = document.getElementById("msg")
        
        form.addEventListener('submit', e => {
            e.preventDefault()
            fetch(scriptURL, { method: 'POST', body: new FormData(form) })
            .then(response => {
                msg.innerHTML = "Отправлено"
                setTimeout(function () {
                        msg.innerHTML = ""
                    }, 50)
                    form.reset()
                })
                .catch(error => console.error('Error!', error.message))
            })
            </script>
</body>

            <style>

                
            </style>
</html>