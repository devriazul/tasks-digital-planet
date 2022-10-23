<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategories;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\KnowledgeBase\KnowledgeBaseStore;

class KnowledgeBaseController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.knowledgebase';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $viewPermission = user()->permission('view_knowledgebase');
        abort_403(!in_array($viewPermission, ['all', 'added']));

        $this->categories = KnowledgeBaseCategories::all();
        $this->knowledgebases = KnowledgeBase::all();

        if (in_array('employee', user_roles()) && !in_array('admin', user_roles())) {
            $this->knowledgebases = $this->knowledgebases->where('to', 'employee');
        }

        if (in_array('client', user_roles()) && !in_array('admin', user_roles())) {
            $this->knowledgebases = $this->knowledgebases->where('to', 'client');
        }

        if (user()->permission('view_knowledgebase') == 'added' && !in_array('admin', user_roles())) {
            $this->knowledgebases = $this->knowledgebases->where('added_by', user()->id);
        }

        $knowledgeBase_count = array();
        $knowledgeBase_index = 0;

        foreach($this->categories as $category)
        {
            $data = KnowledgeBaseCategories::find($category->id);
            $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->count();

            if (in_array('employee', user_roles()) && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('to', 'employee')->count();
            }

            if (in_array('client', user_roles()) && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('to', 'client')->count();
                $this->knowledgebases = $this->knowledgebases;
            }

            if (user()->permission('view_knowledgebase') == 'added' && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('added_by', user()->id)->where('to', 'client')->count();
            }


            $knowledgeBase_count[$knowledgeBase_index]['category_id'] = $category->id;
            $knowledgeBase_index++;
        }

        $this->count = collect($knowledgeBase_count);

        return view('knowledge-base.index', $this->data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->addPermission = user()->permission('add_knowledgebase');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('modules.knowledgeBase.addknowledgebase');
        $this->categories = KnowledgeBaseCategories::all();
        $this->selected_category_id = $id;

        if (request()->ajax()) {
            $html = view('knowledge-base.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'categories' => $this->categories, 'selected_category_id' => $id, 'title' => $this->pageTitle]);
        }

        $this->view = 'knowledge-base.ajax.create';
        return view('knowledge-base.create', $this->data);
    }

    public function store(KnowledgeBaseStore $request)
    {
        $this->addPermission = user()->permission('add_knowledgebase');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $knowledgeBase = new KnowledgeBase();

        $knowledgeBase->to = $request->to;
        $knowledgeBase->heading = $request->heading;
        $knowledgeBase->category_id = $request->category;
        $knowledgeBase->description = str_replace('<p><br></p>', '', trim($request->description));
        $knowledgeBase->added_by = user()->id;
        $knowledgeBase->save();

        return Reply::successWithData(__('messages.knowledgeAdded'), ['redirectUrl' => route('knowledgebase.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->viewPermission = user()->permission('view_knowledgebase');
        abort_403(!in_array($this->viewPermission, ['all', 'added']));

        $this->knowledge = KnowledgeBase::findOrFail($id);

        if (request()->ajax()) {
            $this->pageTitle = __('modules.knowledgeBase.knowledgebase');
            $html = view('knowledge-base.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'knowledge-base.ajax.show';
        return view('knowledge-base.create', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $this->editPermission = user()->permission('edit_knowledgebase');
        abort_403(!in_array($this->editPermission, ['all', 'added']));

        $this->knowledge = KnowledgeBase::findOrFail($id);
        $this->categories = KnowledgeBaseCategories::all();

        $this->pageTitle = __('modules.knowledgeBase.updateknowledge');

        if (request()->ajax()) {
            $html = view('knowledge-base.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'knowledge-base.ajax.edit';

        return view('knowledge-base.create', $this->data);
    }

    public function update(KnowledgeBaseStore $request, $id)
    {
        $this->editPermission = user()->permission('edit_knowledgebase');
        abort_403(!in_array($this->editPermission, ['all', 'added']));

        $knowledge = KnowledgeBase::findOrFail($id);
        $knowledge->heading = $request->heading;
        $knowledge->description = str_replace('<p><br></p>', '', trim($request->description));
        $knowledge->to = $request->to;
        $knowledge->category_id = $request->category;
        $knowledge->added_by = user()->id;
        $knowledge->save();

        return Reply::successWithData(__('messages.knowledgeUpdated'), ['redirectUrl' => route('knowledgebase.index')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deletePermission = user()->permission('delete_knowledgebase');
        abort_403(!in_array($this->deletePermission, ['all', 'added']));

        KnowledgeBase::destroy($id);
        return Reply::successWithData(__('messages.knowledgeDeleted'), ['redirectUrl' => route('knowledgebase.index')]);

    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);
                return Reply::success(__('messages.deleteSuccess'));
        default:
                return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        $this->deletePermission = user()->permission('delete_knowledgebase');
        abort_403(!in_array($this->deletePermission, ['all', 'added']));

        KnowledgeBase::whereIn('id', explode(',', $request->row_ids))->forceDelete();
    }

    public function searchQuery($srch_query = '')
    {
        $model = KnowledgeBase::query();
        $this->categories = KnowledgeBaseCategories::all();

        if ($srch_query != '')
        {
            $model->where('heading', 'LIKE', '%'.$srch_query.'%');
        }

        if (in_array('employee', user_roles()) && !in_array('admin', user_roles())) {
            $model->where('to', 'employee');
        }
        
        if (in_array('client', user_roles()) && !in_array('admin', user_roles())) {
            $model->where('to', 'client');
        }
        
        if (user()->permission('view_knowledgebase') == 'added' && !in_array('admin', user_roles())) {
            $model->where('added_by', user()->id);
        }

        $this->knowledgebases = $model->get();
        $this->addknowledgebasePermission = user()->permission('add_knowledgebase');
        $knowledgeBase_count = array();
        $knowledgeBase_index = 0;
        $knowledgeBase_count1 = array();
        $knowledgeBase_index1 = 0;

        foreach($this->categories as $category)
        {
            $count = $this->knowledgebases->where('category_id', $category->id)->count();
            
            if ($count > 0) {
                $knowledgeBase_count1[$knowledgeBase_index1]['category_id'] = $category->id;
                $knowledgeBase_count1[$knowledgeBase_index1]['counts'] = $count;
            }
            else
            {
                $knowledgeBase_count1[$knowledgeBase_index1]['category_id'] = $category->id;
                $knowledgeBase_count1[$knowledgeBase_index1]['counts'] = 0;
            }

            
            $knowledgeBase_index1++;
        }
      

        foreach($this->categories as $category)
        {
            $count = $this->knowledgebases->where('category_id', $category->id)->count();
            
            if ($count > 0) {
                $knowledgeBase_count[$knowledgeBase_index]['category_id'] = $category->id;
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $count;
            }

            $data = KnowledgeBaseCategories::find($category->id);
            $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->count();

            if (in_array('employee', user_roles()) && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('to', 'employee')->count();
            }

            if (in_array('client', user_roles()) && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('to', 'client')->count();
                $this->knowledgebases = $this->knowledgebases;
            }

            if (user()->permission('view_knowledgebase') == 'added' && !in_array('admin', user_roles())) {
                $knowledgeBase_count[$knowledgeBase_index]['counts'] = $data->knowledgebase->where('added_by', user()->id)->where('to', 'client')->count();
            }

            $knowledgeBase_count[$knowledgeBase_index]['category_id'] = $category->id;
            $knowledgeBase_index++;
        }

        $this->count = collect($knowledgeBase_count1);

        $html = view('knowledge-base.ajax.knowledgedata', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html]);

    }

}
