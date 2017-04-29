<?php

namespace WebstoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class SortProducts
{

    public function sort(Request $request)
    {
        $filter = $request->get('filter');
        $sort = ['a.id', 'desc'];

        if ($filter) {
            switch ($filter) {
                case 1:
                    $sort = ['a.name', 'asc'];
                    break;
                case 2:
                    $sort = ['a.name', 'desc'];
                    break;
                case 3:
                    $sort = ['a.price', 'asc'];
                    break;
                case 4:
                    $sort = ['a.price', 'desc'];
                    break;
                default:
                    break;
            }
        }

        return $sort;
    }

}