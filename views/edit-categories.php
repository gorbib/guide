<h1 class="page-title">Категории</h1>
<form class="category-list container card">
    <?php foreach ($categories as $category) : ?>
    <div class="category-list__item" data-category-id="<?=$category['id']?>">
        <span class="category-list__item__name"><?=$category['name']?></span>
        <button class="category-list__item__remove-button"><img src="/images/garbage.svg"></button>
    </div>
    <?php endforeach; ?>
</form>
<form class="container category-list__new-form" action="/+/categories" method="post">
    <input class="category-list__new-form__input" type="text" name="name" placeholder="Название" required>
    <input class="category-list__new-form__input" type="text" name="alias" pattern="[a-z0-9-]+" placeholder="Адрес" required>
    <button class="btn">Добавить</button>
</div>

<?php ob_start() ?>
<script>
    $('.category-list__item__remove-button').on('click', function(event) {
        event.preventDefault();

        var $item = $(this).parent('.category-list__item');

        $.ajax('/+/categories/' + $item.data('category-id'), {
            type: 'delete',
            dataType: 'json',
            complete: function(){
                $item.remove();
            }
        });
    });
</script>
<?php Flight::view()->set('js', ob_get_clean()) ?>
