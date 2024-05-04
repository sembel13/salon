<?php
/**
 * Страница выборы пользователя
 */

?>


<?php require_once __DIR__ . '/template/header.php'; ?>

<div class="container">
    <div class="row align-items-center justify-content-center vh-100">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    Выберите пользователя системы
                    <br>
                    <a href="selectRole.php?role=<?=ROLE_ADMIN?>" class="card-link">Администратор</a>
                    <br>
                    <a href="selectRole.php?role=<?=ROLE_DIRECTOR?>" class="card-link">Директор</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        text-align: center;
    }
</style>

<?php __DIR__ . '/template/footer.php'; ?>
