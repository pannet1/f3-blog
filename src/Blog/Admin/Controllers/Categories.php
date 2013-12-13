<?php 
namespace Blog\Admin\Controllers;

class Categories extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/categories';

    protected function getModel()
    {
        $model = new \Blog\Admin\Models\Categories;
        return $model;
    }
    
    public function display()
    {
        \Base::instance()->set('pagetitle', 'Categories');
        \Base::instance()->set('subtitle', '');
        
        $model = $this->getModel();
        $categories = $model->getList();
        \Base::instance()->set('categories', $categories );
        
        $state = $model->emptyState()->populateState()->getState();
        \Base::instance()->set('state', $state );
        
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
        
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);       
        \Base::instance()->set('pagination', $pagination );
        
        \Base::instance()->set('selected', 'null' );
        
        $view = new \Dsc\Template;
        echo $view->render('categories/list.php');
    }
    
    public function getDatatable()
    {
        $model = $this->getModel();
        
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
        
        $list = $model->paginate();
        \Base::instance()->set('list', $list );
        
        $pagination = new \Dsc\Pagination($list['total'], $list['limit']);
        \Base::instance()->set('pagination', $pagination );
    
        $view = new \View;
        $html = $view->render('categories/list_datatable.php');
        
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
    
    public function getAll()
    {
        $model = $this->getModel();
        $categories = $model->getList();
        \Base::instance()->set('categories', $categories );

        \Base::instance()->set('selected', 'null' );
        
        $view = new \View;
        $html = $view->render('categories/list_parents.php');
        
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
    
    public function getCheckboxes()
    {
        $model = $this->getModel();
        $categories = $model->getList();
        \Base::instance()->set('categories', $categories );
    
        $selected = array();
        $data = \Base::instance()->get('REQUEST');
        
        $input = $data['category_ids'];
        foreach ($input as $id) 
        {
            $id = $this->inputfilter->clean( $id, 'alnum' );
            $selected[] = array('id' => $id);
        }

        $flash = \Dsc\Flash::instance();
        $flash->store( array( 'metadata'=>array('categories'=>$selected) ) );
        \Base::instance()->set('flash', $flash );
        
        $view = new \View;
        $html = $view->render('categories/checkboxes.php');
    
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
}