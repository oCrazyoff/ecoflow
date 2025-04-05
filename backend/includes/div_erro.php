<?php
if (isset($_SESSION['resposta'])) {
    echo "<div id='erro'>
    " . $_SESSION['resposta'] . "
    </div>";
    unset($_SESSION['resposta']);
}
