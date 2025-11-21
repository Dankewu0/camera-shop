<?php
session_start();
?>
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Магазин фото товаров</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <?php include 'header.php'; ?>

    <div class="asb">
      <main>
        <section>
          <h2 class="main-text">Популярные товары</h2>
          <div class="container">

            <div class="card">
              <div class="product-image"><img src="public/main/canon.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Canon EOS R5</h3>
              <p class="product-desc">
              EOS R5 с матрицей 45 Мп снимает до 20 кадров/с и видео 8K RAW. Стабилизация, автофокус Dual Pixel, видоискатель, сенсорный дисплей 3,15", Wi-Fi, Bluetooth, GPS. Корпус из магниевого сплава, аккумулятор на 490 снимков.
              </p>
              <p class="text-index">Цена: 150 000 руб.</p>
            </div>

            <div class="card">
              <div class="product-image"> <img src="public/main/sony.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Sony A7S III</h3>
              <p class="product-desc">
                A7S III с 12 Мп сенсором обеспечивает отличную съемку при слабом освещении. Поддерживает видео 4K до 120 кадров/с, стабилизацию, OLED-видоискатель, сенсорный дисплей. Корпус защищён от влаги, пыли, аккумулятор на 600 снимков.
              </p>
              <p class="text-index">Цена: 200 000 руб.</p>
            </div>

            <div class="card">
              <div class="product-image"> <img src="public/main/nikon.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Nikon Z6 II</h3>
              <p class="product-desc">
                Полноформатная беззеркалка 24,5 Мп с быстрым EXPEED 6 снимает 14 кадров/с и видео 4K UHD. Прочный влагозащищённый корпус, двойной слот, Wi-Fi и Bluetooth. Заряда хватает примерно на 410 снимков.
              </p>
              <p class="text-index">Цена: 120 000 руб.</p>
            </div>

            <div class="card">
              <div class="product-image"> <img src="public/main/fujifilm.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Fujifilm X-T4</h3>
              <p class="product-desc">
               Беззеркальная камера с 26 Мп X-Trans CMOS 4 и 5-осевой стабилизацией. Снимает сериями до 15 кадров/с и видео 4K/60fps. Наклонный сенсорный дисплей, защищённый корпус, аккумулятор на 500 снимков, есть Wi-Fi и Bluetooth.
              <p class="text-index">Цена: 110 000 руб.</p>
            </div>

            <div class="card">
              <div class="product-image"><img src="public/main/panasonic.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Panasonic GH5</h3>
              <p class="product-desc">
               Видеокамера с 20,3 Мп сенсором Micro Four Thirds и 10-битным 4:2:2 форматом. Поддерживает 4K 60fps, оснащена Dual I.S. 2 для стабилизации и сенсорным экраном. Корпус защищён, аккумулятор на 410 снимков.
              </p>
              <p class="text-index">Цена: 90 000 руб.</p>
            </div>

            <div class="card">
              <div class="product-image"><div class="product-image"><img src="public/main/sigma.png" alt="Canon EOS R5"></div></div>
              <h3 class="text-index">Sigma 35mm f/1.4</h3>
              <p class="product-desc">
                Фикс-объектив с высокой светосилой даёт резкость и красивое боке, идеально для портретов и пейзажей. Гиперзвуковой мотор автофокуса, прочный защищённый корпус и многослойное покрытие против бликов.
              </p>
              <p class="text-index">Цена: 45 000 руб.</p>
            </div>

          </div>

          <h2 class="main-text">Популярные категории товаров</h2>
          <div class="container">
            <div class="card" ><div class="product-image"> <img src="public/main/leica.png" alt="Canon EOS R5"> </div><h3 class="text-index">Фотокамеры</h3></div>
            <div class="card"><div class="product-image"> <img src="public/main/video.png" alt="Canon EOS R5"></div><h3 class="text-index">Видеокамеры</h3></div>
            <div class="card"><div class="product-image"> <img src="public/main/objective.png" alt="Canon EOS R5"></div><h3 class="text-index">Объективы</h3></div>
            <div class="card"><div class="product-image"> <img src="public/main/accessories.png" alt="Canon EOS R5"></div><h3 class="text-index">Аксессуары</h3></div>
            <div class="card"><div class="product-image"> <img src="public/main/levenhuk.png" alt="Canon EOS R5"></div><h3 class="text-index">Штативы</h3></div>
            <div class="card"><div class="product-image"> <img src="public/main/backpack_photo.png" alt="Canon EOS R5"></div><h3 class="text-index">Сумки и рюкзаки</h3></div>
          </div>

          <h2 class="main-text">Дополнения к камере</h2>
          <div class="container">
            <div class="card">
              <div class="product-image"><img src="public/main/lightfilter.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Светофильтр ND</h3>
              <p class="product-desc">Светофильтр ND позволяет контролировать количество света, попадающего на сенсор, для создания эффектов размытия движения, длинной выдержки и правильной экспозиции при ярком освещении.</p>
              <p class="text-index">Цена: 2 000 руб.</p>
            </div>
            <div class="card">
              <div class="product-image"> <img src="public/main/lightcanon.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Вспышка Canon</h3>
              <p class="product-desc">Вспышка Canon с высокой мощностью и гибкими настройками. Позволяет корректировать освещение в студийной и выездной съемке, синхронизацию с камерой и плавное освещение объектов.</p>
              <p class="text-index">Цена: 7 000 руб.</p>
            </div>
            <div class="card">
              <div class="product-image"><img src="public/main/manfrotto.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Штатив Manfrotto</h3>
              <p class="product-desc">Надёжный штатив Manfrotto обеспечивает стабильную фиксацию и простую установку. Прочная алюминиевая конструкция, регулируемая высота и поворотная головка делают его универсальным для фото и видео.</p>
              <p class="text-index">Цена: 10 000 руб.</p>
            </div>
            <div class="card">
              <div class="product-image"><img src="public/main/sdcard.png" alt="Canon EOS R5"></div>
              <h3  class="text-index">Карта памяти 128GB</h3>
              <p class="product-desc">Карта памяти 128GB высокой скорости чтения и записи, поддерживающая фото и видео высокого разрешения. Надежное хранение данных с защитой от влаги и ударов.</p>
              <p class="text-index">Цена: 1 500 руб.</p>
            </div>
            <div class="card">
              <div class="product-image"><img src="public/main/bag.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Сумка для камеры</h3>
              <p class="product-desc">Сумка для камеры обеспечивает надежную защиту оборудования, удобные отделения для объективов и аксессуаров, а также комфортную переноску даже при длительных съемках.</p>
              <p class="text-index">Цена: 3 000 руб.</p>
            </div>
            <div class="card">
              <div class="product-image"><img src="public/main/battery.png" alt="Canon EOS R5"></div>
              <h3 class="text-index">Запасной аккумулятор</h3>
              <p class="product-desc">Запасной аккумулятор позволяет продлить время работы камеры без необходимости подзарядки. Полностью совместим с моделью камеры, обеспечивает надежную автономность при длительных съемках.</p>
              <p class="text-index">Цена: 4 000 руб.</p>
            </div>
          </div>

          <div class="welcome-block">
            <p class="welcome-text">Мы рады видеть вас на нашем сайте.</p>
            <p class="welcome-text">
              В Интернет-магазине Wunsche вы найдете широкий ассортимент товаров для профессиональных фотографов и любителей. Наша компания предлагает только качественную продукцию от ведущих производителей фототехники и видеотехники.
            </p>
            <p class="welcome-text">
              В нашем магазине вы сможете купить фотоаппараты, видеокамеры, объективы, аксессуары для фотоаппаратов, а также другое оборудование для съемки. У нас самые низкие цены на товары, но если Вы найдете дешевле, мы обязательно снизим цену! Мы предоставляем гарантию и обеспечиваем быструю доставку по всей стране. Если у вас возникнут вопросы, наши специалисты всегда готовы помочь вам с выбором и ответить на все ваши вопросы. Мы стремимся сделать процесс покупки максимально удобным и приятным для наших клиентов.
            </p>
          </div>
        </section>
      </main>
    </div>

    <footer class="footer">
      <div class="footer-container">
        <div class="footer-column">
          <h3>О компании</h3>
          <p>Мы занимаемся созданием качественных продуктов с 2010 года. Наша миссия - делать мир лучше.</p>
        </div>
        <div class="footer-column">
          <h3>Контакты</h3>
          <p>Email: info@example.com</p>
          <p>Телефон: +7 (999) 123-45-67</p>
          <p>Адрес: Москва, ул. Примерная, 123</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Все права защищены</p>
      </div>
    </footer>
  </body>
</html>
