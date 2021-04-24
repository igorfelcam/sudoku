<?php
require 'controllers/Sudoku.php';
require 'services/Board.php';
require 'services/Search.php';

$sudoku = new \Controllers\Sudoku(
                new \Services\Board,
                new \Services\Search
            );


$sudoku->loadByFile($_FILES['sudoku_file']['tmp_name']);

/*
https://www.moodle.unisinos.br/pluginfile.php/1334008/mod_resource/content/1/aula_02_01-Resol-prob-busca-cega.pdf


BUSCA CEGA EM LARGURA

function BL(Estado inicial): Nodo
    Queue fronteira
    fronteira.add(new Nodo(inicial))
    while not fronteira.isEmpty() do
        Nodo n ß fronteira.remove()
        if n.getEstado().éMeta() then
            return n
        end if
        if n.getEstado() não está em fechado then
            fechado.add(n.getEstado())
            fronteira.add(n.sucessores())
        end if
    end while
return null

OBTER SUCESSORES DO NÓ

function sucessores (Nodo n, acao): lista de nós
    for (cada ação em n.getEstado()) do
        s ß cria novo nó
        s.estado = resultado da ação em n
        s.pai = n
        s.acao = acao
        s.custo_caminho = n.custo_caminho + custo_passo (n, acao, s)
        s.profundidade = n.profundidade + 1
        adicionar s a sucessores
retornar sucessores


BUSCA CEGA EM PROFUNDIDADE

function BP(Estado inicial, int m): Nodo
    Stack fronteira
    fronteira.add(new Nodo(inicial))
    while not fronteira.isEmpty() do
        Nodo n ß fronteira.remove()
        if n.getEstado().éMeta() then
            return n
        end if
        if n.getProfundidade() < m then
            fronteira.add(n.sucessores())
        end if
    end while
return null


BUSCA CEGA COM APROFUNDAMENTO ITERATIVO

function BPI(Estado inicial): Nodo
    int p ß 1
    loop
        Nodo n ß BP(inicial, p)
        if n <> null then
            return n
        end if
        p ß p + 1
    end loop

*/

ini_set('max_execution_time', 180);

$resolvedSudoku = $sudoku->resolveSudoku($sudoku->sudokuBoard, true);
// $resolvedSudoku = $sudoku->resolveSudoku($sudoku->sudokuBoard, false);

echo "<style>body { display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }</style>";

$last_item = $resolvedSudoku['search_tree'][array_key_last($resolvedSudoku['search_tree'])];
echo "<section><div>Generated nodes: " .$last_item['node']. "</div>";

$seconds_execution = $resolvedSudoku['end_time'] - $resolvedSudoku['start_time'];
echo "<div>Seconds execution: " .$seconds_execution. "s</div></section>";

echo "<table style='border: 1px solid black;'>";

$sudoku_length  = count($resolvedSudoku['solution']);
$line_counter   = 0;

foreach ($resolvedSudoku['solution'] as $line_key => $line) {
    echo "<tr>";

    $line_counter++;
    $line_length = count($line);

    $border_bottom  = false;
    $border_top     = false;
    if ($line_counter == $line_length && ($sudoku_length - 1) > $line_key) {
        $border_bottom  = true;
        $line_counter   = 0;
    }
    if ($line_counter === 1 && $line_key > 0) {
        $border_top     = true;
    }

    $item_counter = 0;
    array_walk_recursive(
        $line,
        function($item, $key) use (&$item_counter, $sudoku_length, $line_length, $border_bottom, $border_top) {
            $item_counter++;

            echo "<td style='border: 1px solid black; padding: .75rem 1rem;";

            $style = "";
            if ($border_bottom) {
                $style = "border-bottom: 3px solid black;";
            }
            else if ($border_top) {
                $style = "border-top: 3px solid black;";
            }

            if ($item_counter > 1 && $key == 0) {
                $style .= "border-left: 3px solid black;";
            }
            else if (($key + 1) === $line_length && ($sudoku_length / $item_counter) != 1) {
                $style .= "border-right: 3px solid black;";
            }

            echo "$style'>$item</td>";
        },
        $item_counter
    );

    echo "</tr>";
}

echo "</table>";
