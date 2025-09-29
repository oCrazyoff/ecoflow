<?php
if (isset($_SESSION['resposta'])): ?>
    <!--div de erro-->
    <div id="div-erro">
        <i class="bi bi-info-circle-fill"></i>
        <?= htmlspecialchars($_SESSION['resposta']) ?>
    </div>
    <?php
    unset($_SESSION['resposta']);
endif;
?>