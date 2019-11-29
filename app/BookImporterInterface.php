<?php


namespace App;


interface BookImporterInterface
{
    /**
     * @param $isbn
     * @return mixed
     */
    public function import($isbn);
}
