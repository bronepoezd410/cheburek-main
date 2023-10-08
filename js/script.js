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

// Код для отправки данных на сервер при сохранении изменений
const editSubmitButton = document.getElementById('editSubmit');
editSubmitButton.addEventListener('click', () => {
    const itemId = document.getElementById('itemId').value;
    const itemName = document.getElementById('itemName').value;
    const itemPrice = document.getElementById('itemPrice').value;
    const selectedCategoryId = categorySelect.value;


    const categoryError = document.getElementById('categoryError'); // Элемент для отображения ошибки

    // Проверяем, выбрана ли категория
    if (!selectedCategoryId) {
        categoryError.textContent = 'Пожалуйста, выберите категорию.'; // Отображаем ошибку
        return; // Останавливаем отправку формы
    }

    // Очищаем ошибку, если категория выбрана
    categoryError.textContent = '';

    
    // Отправляем AJAX-запрос на сервер для обновления данных о товаре
    // Внесите изменения в JSON-объект для запроса
    fetch('/config/update.php', {
        method: 'POST',
        body: JSON.stringify({
            itemId: itemId, // Добавьте itemId
            itemName: itemName,
            itemPrice: itemPrice,
            categoryId: selectedCategoryId,
        }),
        headers: {
            'Content-Type': 'application/json'
        }
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
    console.log(itemId);
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