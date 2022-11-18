<?php

namespace App\Admin\Controllers;

use App\Events\MessageEvent;
use App\Events\NoticeEvent;
use App\Models\Notice;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Log;

class NoticeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Notice';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notice());

        $grid->column('id', __('Id'));
        $grid->column('content', __('通知内容'));
        $grid->column('type', __('通知类型'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Notice::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('content', __('通知内容'));
        $show->field('type', __('通知类型'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Notice());

        $form->number('id', __('Id'));
        $form->textarea('content', __('通知内容'));
        $form->radioCard('type', __('通知类型'))->options(['1' => '站内通知', '2' => 'Line公众号通知'])->default('m');
        //保存后回调
        $form->saved(function (Form $form) {
            if ($form->type == 2) {
                $httpClient = new CurlHTTPClient(env("LINE_BOT_CHANNEL_ACCESS_TOKEN"));
                $bot = new LINEBot($httpClient, ['channelSecret' => env("LINE_BOT_CHANNEL_SECRET")]);

                $textMessageBuilder = new TextMessageBuilder($form->content);
                $response = $bot->broadcast($textMessageBuilder);//群发消息
                Log::info("Line消息通知:", [$response->getHTTPStatus(), $response->getRawBody()]);
            } else {
                event(new NoticeEvent($form->content));
            }

        });
        return $form;
    }
}
