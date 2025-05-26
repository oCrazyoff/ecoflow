<?php
if (isset($_SESSION['resposta'])) {
    echo "<div id='erro'><i class='bi bi-info-circle-fill'></i> " . $_SESSION['resposta'] . "</div>";
    unset($_SESSION['resposta']);
}
