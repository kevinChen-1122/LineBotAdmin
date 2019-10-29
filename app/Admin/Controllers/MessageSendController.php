<?php

namespace App\Admin\Controllers;

use App\Models\MessageSend;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

use App\Helpers\GetDataOption;
use App\Helpers\GetMatchData;

use App\Admin\Actions\SendMessage;

class MessageSendController extends Controller
{
	
   use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Line Bot 訊息發送')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Line Bot 訊息發送')
            ->description('检视')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Line Bot 訊息發送')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Line Bot 訊息發送')
            ->description('新建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MessageSend);

		// 關閉選擇器
		$grid->disableRowSelector();
		$grid->disableExport();
		$grid->disableColumnSelector();
		$grid->disableFilter();
		//$grid->disableCreateButton();
		//$grid->disableActions();
		//自訂
		$grid->filter(function($filter){
			$filter->disableIdFilter();
				// 在这里添加字段过滤器
			/*
				$filter->group('play_count', '可游玩次数', function ($group) {
					$group->equal('等于');
					$group->notEqual('不等于');
					$group->gt('大于');
					$group->lt('小于');
					$group->nlt('大于等于');
					$group->ngt('小于等于');
				});
			*/
		});
		// 關閉搜尋
		//$grid->disableFilter(); 
		$grid->actions(function ($actions) {
			
			//$actions->disableEdit();
			
			$actions->disableView();
			
			//$actions->disableDelete();
			
			$actions->add(new SendMessage);
		});
		/*
		$grid->tools(function ($tools) {
			$switch = $this->nowSwitch();
			$tools->append(new \App\Admin\Extensions\Tools\UserGender(admin_base_path('notice/check_switch'),$switch));
		});*/
		$grid->column('bot_id', 'Line Bot')->display(function ($bot_id) {
			return GetMatchData::getMatchBotId($bot_id);
		});
		$grid->column('message', '發送訊息')->limit(20);
		$grid->column('user_id', '發送對象')->display(function ($user_id) {
			return GetMatchData::getMatchUserIdToNameArray($user_id);
		})->label('success');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MessageSend::findOrFail($id));

        $show->setting_id('Setting id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MessageSend);

		$form->tools(function (Form\Tools $tools) {
			$tools->disableView();
			$tools->disableDelete();
			/*
			$tools->disableList();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
		});
		
		$form->footer(function ($footer) {

			// 去掉`重置`按钮
			//$footer->disableReset();

			// 去掉`提交`按钮
			//$footer->disableSubmit();

			// 去掉`查看`checkbox
			$footer->disableViewCheck();

			// 去掉`继续编辑`checkbox
			$footer->disableEditingCheck();

			// 去掉`继续创建`checkbox
			$footer->disableCreatingCheck();

		});
		$form->select('bot_id', 'Line Bot')->required()->options(GetDataOption::getBotIdOption());
		$form->textarea('message', '發送訊息')->required();
		$form->multipleSelect('user_id','發送對象')->required()->options(GetDataOption::getUserIdOption());
        return $form;
    }
}

