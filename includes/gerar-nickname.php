<?php
function gerarNicknameAleatorio() {
    $adjetivos = [
        'Veloz', 'Sabio', 'Feliz', 'Calmo', 'Forte', 'Nobre', 'Gentil', 'Firme',
        'Valente', 'Astuto', 'Leal', 'Digno', 'Amigo', 'Livre', 'Grato'
    ];
    
    $animais = [
        'Aguia', 'Panda', 'Lobo', 'Leao', 'Tigre', 'Urso', 'Falcao', 'Apoio', 
        'Coruja', 'Raposa', 'Cisne', 'Lince', 'Touro', 'Gato', 'Koa'
    ];

    $adj = $adjetivos[array_rand($adjetivos)];
    $ani = $animais[array_rand($animais)];
    $num = rand(10, 999); 

    return $adj . $ani . $num; //gera o apelido aleatório
}
?>