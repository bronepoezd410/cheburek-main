let navbar = document.querySelector('.navbar');

document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
    searchForm.classList.remove('active');
    cartItem.classList.remove('active');
}

let cartItem = document.querySelector('.cart-items-container');
document.querySelector('#cart-btn').onclick = () => {
    cartItem.classList.toggle('active');
}

let searchForm = document.querySelector('.search-form');

document.querySelector('#search-btn').onclick = () => {
    searchForm.classList.toggle('active');
    navbar.classList.remove('active');
    cartItem.classList.remove('active');
}


window.onscroll = () => {
    navbar.classList.remove('active');
    searchForm.classList.remove('active');
    cartItem.classList.remove('active');
}




const editButtons = document.querySelectorAll('.edit-btn');

editButtons.forEach((button) => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const modal = document.getElementById('openModal');
        const itemId = button.closest('.box').getAttribute('data-item-id');

        // Отправляем AJAX-запрос на сервер для получения данных о товаре по его ID
        fetch(`config/get_item_data.php?itemId=${itemId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    console.error(data.error);
                } else {
                    // Заполняем форму данными о товаре из ответа сервера
                    document.getElementById('itemId').value = itemId;
                    document.getElementById('itemName').value = data.item_name;
                    document.getElementById('itemPrice').value = data.price;
                    const itemImage = document.getElementById('itemImage');

                    if (data.image_url) {
                        itemImage.src = data.image_url;
                    } else {
                        itemImage.src = ''; // Если изображения нет, очищаем src
                    }

                    // Открываем модальное окно
                    modal.style.display = 'block';

                    const categorySelect = document.getElementById('categorySelect');
                    categorySelect.value = data.categoryId;
                }
            })
            .catch((error) => {
                console.error(error);
            });
    });
});

// Обработчик изменения файла изображения
const itemImageInput = document.getElementById('itemImageInput');
itemImageInput.addEventListener('change', (e) => {
    const itemImage = document.getElementById('itemImage');
    const file = e.target.files[0];

    if (file) {
        const imageUrl = URL.createObjectURL(file);
        itemImage.src = imageUrl;

    } else {
        itemImage.src = '';
    }
});

// Код для отправки данных на сервер при сохранении изменений
const editSubmitButton = document.getElementById('editSubmit');
editSubmitButton.addEventListener('click', () => {
    const itemId = document.getElementById('itemId').value;
    const itemName = document.getElementById('itemName').value;
    const itemPrice = document.getElementById('itemPrice').value;



    const selectedCategoryId = categorySelect.value;
    const itemImageFile = itemImageInput.files[0]; // Получаем выбранный файл изображения

    const categoryError = document.getElementById('categoryError'); // Элемент для отображения ошибки

    // Проверяем, выбрана ли категория
    if (!selectedCategoryId) {
        categoryError.textContent = 'Пожалуйста, выберите категорию.'; // Отображаем ошибку
        return; // Останавливаем отправку формы
    }

    // Очищаем ошибку, если категория выбрана
    categoryError.textContent = '';

    // Создаем объект FormData для отправки данных на сервер
    const formData = new FormData();
    formData.append('itemId', itemId);
    formData.append('itemName', itemName);
    formData.append('itemPrice', itemPrice);
    formData.append('categoryId', selectedCategoryId);

    // Добавляем файл изображения в FormData, если он выбран
    if (itemImageFile) {
        formData.append('itemImage', itemImageFile);
    }

    // Отправляем данные на сервер
    fetch('/config/update.php', {
        method: 'POST',
        body: formData
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Закрываем модальное окно после успешного обновления
                const modal = document.getElementById('openModal');
                modal.style.display = 'none';

                // Обновляем информацию о товаре на странице
                const itemBox = document.querySelector(`.box[data-item-id="${itemId}"]`);
                const itemNameElement = itemBox.querySelector('h3');
                const itemPriceElement = itemBox.querySelector('.price');

                itemNameElement.textContent = itemName;
                itemPriceElement.textContent = itemPrice + '₽';
            } else {
                console.error(data.error);
            }
        })
        .catch((error) => {
            console.error(error);
        });
});

// Код для закрытия модального окна при нажатии на "X"
const closeModalButton = document.querySelector('.close');

closeModalButton.addEventListener('click', () => {
    const modal = document.getElementById('openModal');
    modal.style.display = 'none';
});




const categorySelect = document.getElementById('categorySelect');

// Добавляем обработчик события для выбора категории
categorySelect.addEventListener('change', (event) => {
    // Получаем выбранное значение
    const selectedCategoryId = event.target.value;

    // Вы можете использовать выбранное значение (selectedCategoryId) в своем коде для дальнейшей обработки
    console.log('Выбранная категория:', selectedCategoryId);
});


document.addEventListener('DOMContentLoaded', function () {
    // Получите itemId из вашей HTML-разметки
    const itemId = document.getElementById('itemId').value;
    // Выполните AJAX-запрос после получения itemId
    fetch('/config/categories.php')
        .then((response) => response.json())
        .then((data) => {
            if (Array.isArray(data)) {
                const categoryIdSelect = document.getElementById('categorySelect');

                // Очистите существующие опции (если они есть)
                categoryIdSelect.innerHTML = '';

                // Добавьте новые опции на основе полученных данных
                data.forEach((category) => {
                    const option = document.createElement('option');
                    option.value = category.id; // Установите значение опции
                    option.textContent = category.name; // Установите текст опции
                    categoryIdSelect.appendChild(option); // Добавьте опцию в выпадающий список

                    // Если ID категории совпадает с ID категории товара, установите эту опцию как выбранную
                    if (category.id === itemId) {
                        option.selected = true;
                    }
                });
            } else {
                console.error('Ошибка: Данные не являются массивом.', data);
            }
        })
        .catch((error) => {
            console.error('Произошла ошибка при получении данных:', error);
        });
});

function filterItems(category) {
    const boxes = document.querySelectorAll('.box');

    boxes.forEach((box) => {
        const itemCategory = box.getAttribute('data-category');

        if (category === 'all' || category === itemCategory) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });
}

// Обработка кликов по кнопкам категорий
const categoryButtons = document.querySelectorAll('.sorting-btn');

categoryButtons.forEach((button) => {
    button.addEventListener('click', (e) => {
        const category = e.target.getAttribute('data-category');
        filterItems(category);
    });
});

// Обработка клика по кнопке "Все"
const allButton = document.querySelector('.sorting-btn[data-category="all"]');

allButton.addEventListener('click', () => {
    filterItems('all');
});

// Функция для обновления количества в корзине
function updateCartCount() {
    $.ajax({
        url: '../config/get_cart_count.php', // Путь к серверному скрипту, который вернет количество товаров в корзине
        type: 'GET',
        success: function(data) {
            // Обновляем элемент с количеством
            $('#cart-count').text(data);
        },
        error: function(error) {
            console.log(error);
        }
    });
}

$(document).ready(function() {
    $(".buy-btn").click(function(e) {
        e.preventDefault();
        var item_id = $(this).data("item-id");
        var quantity = 1; // Вы можете задать желаемое количество товара
        $.ajax({
            type: "POST",
            url: "../config/add_to_cart.php",
            data: { item_id: item_id, quantity: quantity },
            success: function(response) {
                // Обработка успешного добавления товара
                updateCartCount();
                alert(response); // Вывод сообщения пользователю
            },
        });
    });
});








// Обработчик события для открытия модального окна
$("#open-modal-button").click(function() {
    $.ajax({
        type: "GET",
        url: "../config/get_cart_items.php", // Путь к серверному скрипту для получения товаров в корзине
        dataType: "json",
        success: function(data) {
            if (data.length > 0) {
                // Очищаем содержимое модального окна
                $("#modal-content").empty();

                // Создаем список товаров и добавляем его в модальное окно
                var itemList = $("<ul>");
                $.each(data, function(index, item) {
                    var listItem = $("<li>").text(item.item_name + " - " + item.quantity);
                    itemList.append(listItem);
                });
                $("#modal-content").append(itemList);
            } else {
                // Если корзина пуста, отобразите сообщение
                $("#modal-content").text("Корзина пуста.");
            }
        }
    });
});

// Открывает модальное окно корзины
document.querySelector('#show-cart-btn').addEventListener('click', function() {
    // Отправляем запрос на серверный скрипт для получения товаров в корзине
    fetch('../config/get_cart_items.php')
    .then(response => response.json())
    .then(data => {
        const cartItemsList = document.querySelector('#cart-items-list');
        cartItemsList.innerHTML = '';  // Очищаем список

        if (data.length > 0) {
            data.forEach(item => {
                // Создаем HTML-элемент карточки для каждого товара
                const itemCard = document.createElement('div');
                itemCard.className = 'item-card';   // Используем класс для стилизации карточки
                
                // Внутри карточки сгенерируем HTML с изображением товара
                const innerHtml = `
                    <div class="item-image-container">
                        <img class="item-image" src="${item.image_url}" alt="${item.item_name}">
                    </div>
                    <div class="item-info">
                        <h2 class="item-title">${item.item_name}</h2>
                        <p class="item-quantity">Количество: ${item.quantity}</p>
                        <p class="item-price">Цена: ${item.price}</p>
                        <br>
                    </div>`;
                itemCard.innerHTML = innerHtml;
                cartItemsList.appendChild(itemCard);
            });
            } else {
                // Если корзина пуста, отобразите сообщение
                const emptyCartMessage = document.createElement('p');
                emptyCartMessage.textContent = 'Корзина пуста';
                cartItemsList.appendChild(emptyCartMessage);
            }
        });

    // Открываем модальное окно
    document.querySelector('#cart-modal').style.display = 'block';
});



