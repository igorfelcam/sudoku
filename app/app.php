<?php
require 'controllers/Sudoku.php';
require 'services/Board.php';

$sudoku = new \Controllers\Sudoku(new \Services\Board);


$sudoku->loadByFile($_FILES['sudoku_file']['tmp_name']);




echo "<pre>";
var_dump(
    $sudoku->sudokuBoard
);
die;
