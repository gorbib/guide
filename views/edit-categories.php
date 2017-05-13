<h1 class="page-title">Категории</h1>
<form class="category-list container">
    <?php foreach ($categoriesList as $category) : ?>
    <div class="category-list__item" data-category-id="<?=$category['id']?>">
        <span class="category-list__item__name"><?=$category['name']?></span>
        <button class="category-list__item__remove-button"><img src="/images/garbage.svg"></button>
    </div>
    <?php endforeach; ?>
</form>
<form class="container category-edit-form" action="/+/categories" method="post">
    <div class="category-edit-form__main">
        <input class="category-edit-form__field" type="text" name="name" placeholder="Название" maxlength="60" required>
        <input class="category-edit-form__field" type="text" name="alias" pattern="[a-z0-9-]+" placeholder="Адрес" required>
    </div>
    <button class="btn category-edit-form__submit-button">Добавить</button>
</div>

<?php ob_start() ?>
<script>
    $('.category-list__item__remove-button').on('click', function(event) {
        event.preventDefault();

        var $item = $(this).parent('.category-list__item');

        $.ajax('/+/categories/' + $item.data('category-id'), {
            type: 'delete',
            dataType: 'json',
            complete: function(response){
                if(response.responseJSON.success) {
                    $item.remove();
                } else {
                    alert('Нельзя удалять категорию, в которой есть места. Их сначала надо перенеси в другую категорию.')
                }

            }
        });
    });
</script>
<?php Flight::view()->set('js', ob_get_clean()) ?>
