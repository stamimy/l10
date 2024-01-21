<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('product', 'products');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name'      => 'row_number',
            'type'      => 'row_number',
            'label'     => '#',
            'orderable' => false,
        ])->makeFirstColumn();

        CRUD::addColumn([
            'label'       => 'Kategori Produk',
            'type'        => 'select',
            'name'        => 'category.id',
            'entity'      => 'category',
            'attribute'   => 'name', // combined name & date column
            'model'       => 'App\Models\Category',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('category', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'ilike', '%'.$searchTerm.'%');
                });
            }
        ]);

        CRUD::addColumn([
            'name'      => 'name',
            'label'     => 'Nama Produk',
            'type'      => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('name', 'ilike', '%'.$searchTerm.'%');
            }
        ]);

        CRUD::addColumn([
            'name'  => 'price_buy', // The db column name
            'label' => 'Harga Beli (IDR)', // Table column heading
            'type'  => 'number',
            //'decimals'      => 2,
            'dec_point'     => ',',
            'thousands_sep' => '.',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('price_buy', 'like', '%'.$searchTerm.'%');
            }
         ]);

         CRUD::addColumn([
            'name'  => 'price_sell', // The db column name
            'label' => 'Harga Jual (IDR)', // Table column heading
            'type'  => 'number',
            //'decimals'      => 2,
            'dec_point'     => ',',
            'thousands_sep' => '.',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('price_sell', 'like', '%'.$searchTerm.'%');
            }
         ]);

         CRUD::addColumn([
            'name'  => 'stock', // The db column name
            'label' => 'Stok Produk', // Table column heading
            'type'  => 'number',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('stock', 'like', '%'.$searchTerm.'%');
            }
         ]);

        //CRUD::setFromDb(); // set columns from db columns.
        //CRUD::removeColumn('category_id');
        //CRUD::removeColumn('image');

        CRUD::addColumn([
            'name'      => 'image',
            'label'     => 'Profile image',
            'type'      => 'image',
            'prefix' => 'storage/'
        ]);


        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProductRequest::class);
        
        CRUD::field([
            'label'     => "Kategori Produk",
            'type'      => 'select',
            'name'      => 'category',
            'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->get();
             }),
         ]);

         CRUD::field([
            'name'  => 'name',
            'label' => "Nama Produk",
            'type'  => 'text'
        ]);

        CRUD::field([
            'name'      => 'price_buy',
            'label'     => 'Harga Beli',
            'type'      => 'number',
            'prefix'    => "IDR",
            'suffix'    => ".00",
        ])->on('saving', function ($entry) {
            $entry->price_sell = 30 * $entry->price_buy / 100;
        });

        CRUD::field([
            'name'      => 'stock',
            'label'     => 'Stok Produk',
            'type'      => 'number',
        ]);

        CRUD::field([
            'name'      => 'image',
            'label'     => 'Image',
            'type'      => 'upload',
            'withFiles' => [
                'disk' => 'public', // the disk where file will be stored
                'path' => 'products', // the path inside the disk where file will be stored
            ]
        ]);

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
