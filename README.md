<img src="https://xn--80aaafs0abz2a9d.xn--p1ai/images/mountain.svg" width="300">
# Путеводитель по достопримечательностям Качканара
Сайт разработан и поддерживается библиотекой им. Селянина. На нём мы публикуем описания, историю и фотографии разных интересных мест города.

<img src="https://res.cloudinary.com/library/image/upload/v1488273900/guide/%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA_%D1%8D%D0%BA%D1%80%D0%B0%D0%BD%D0%B0_2017-02-28_%D0%B2_14.23.06.png">

## Использование
Лицензия MIT, а значит вы можете свободно использовать этот проект в своих целях. Сделать каталог достопримечательностей своего города, например.
Предлагайте новые функции и изменения.

## Установка
### 1. [Скачайте архив](https://github.com/gorbib/guide/archive/master.zip) или клонируйте репозиторий через git
`git clone https://github.com/gorbib/guide.git`

### 2. Установите зависимости через [composer](http://getcomposer.org/)
`composer install`

### 3. Настройте
#### 1. Создайте структуру базы данных. Дамп лежит в `config/dump.sql`

#### 2. Измените настройки в файле `config/config.php`
```php
return array(
    // Конфигурация базы данных
    'db' => array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'name' => ''
    ),
    // Для загрузки и хранения изображений мы используем Cloudinary (https://cloudinary.com)
    // Укажите здесь свои параметры для этого сервиса
    'cloudinary' => array(
        'cloud' => '',
        'key' => '',
        'api-secret' => ''
    ),
    // Если установлено значение true, то для пользователя будет открыт доступ к редактированию материалов
    'admin' => false,
    // Описание для главной и страниц без описания
    'description' => ''
);
```
#### 3. Настройте сервер
Для работы роутинга важно, чтобы все запросы перенаправлялись на __index.php__.

Если у вас apache, то делать ничего не надо, .htaccess прилагается, если nginx, то добавьте перенаправление в раздел `server` вашего конфига:
```
location / {
    try_files $uri $uri/ /index.php;
}
```

