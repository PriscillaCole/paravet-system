<?php

namespace App\Admin\Controllers;

use App\Models\ServiceProvider;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\Utils;
use Encore\Admin\Facades\Admin;
use Carbon\Carbon;


class ServiceProviderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Input Providers';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ServiceProvider());

        
        $grid->export(function ($export) {
        
            $export->originalValue(['status',]);
            $export->except(['created_at','profile_picture',]);
           
        });

        $grid->filter(function($filter){
            $filter->disableIdFilter();
            $filter->like('name', 'Name');
            $filter->like('owner_name', 'Owner name');
            $filter->like('class_of_service', 'Class of service');
            $filter->between('date_of_registration', 'Date of registration')->date();
            $filter->like('physical_address', 'Physical address');
            $filter->like('email', 'Email');
            $filter->like('postal_address', 'Postal address');
            $filter->like('other_services', 'Other services');
            $filter->like('distroict_of_operation', 'District of operation');
            $filter->like('tin_number_business', 'Tin number business');
            $filter->like('tin_number_owner', 'Tin number owner');
            $filter->between('created_at', 'Filter by date registered')->date();
        });

      //show a user only their records if they are not an admin
      if (!Admin::user()->inRoles(['administrator','ldf_admin'])) {
          $grid->model()->where('user_id', Admin::user()->id);
        }
        //disable batch actions
        $grid->disableBatchActions();

        //order of table
        $grid->model()->orderBy('id', 'desc');

        //disable action buttons appropriately
        Utils::disable_buttons('ServiceProvider', $grid);

        $grid->column('logo', __('Logo'))->image('', 50, 50);
        $grid->column('name', __('Name'));
        $grid->column('provider_type', __('Provider type'));
        $grid->column('physical_address', __('Physical address'));
        $grid->column('primary_phone_number', __('Primary phone number'));
        $grid->column('email', __('Email'));
      
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ServiceProvider::findOrFail($id));
         //delete notification after viewing the form
         Utils::delete_notification('ServiceProvider', $id);
 
         $provider = ServiceProvider::findorFail($id);
         return view('provider_profile', compact('provider'));

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ServiceProvider());
        if($form->isCreating()){
            $form->hidden('status')->default('Pending');
            $form->hidden('added_by')->default(Admin::user()->id);
        }
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
           
        });

        $form->radio('provider_category', __('Provider category'))->options
        (['services' => 'Services', 'products' => 'Products','drugs'=>'Drugs'])->rules('required')
        ->when('services', function (Form $form) {
            $form->select('provider_type', __('Provider type'))->options([
                'Veterinary' => 'Veterinary clinic',
                'Breeding Services' => 'Breeding services',
                'Artificial Insemination' => 'Artificial insemination',
                'Vaccination' => 'Vaccination',
                'Live_stock Transportation' => 'Live stock transportation',
                'Livestock Insurance' => 'Livestock insurance',
                'Diagnostic Laboratory' => 'Diagnostic Laboratory',
                'Training Centre' => 'Training Centre'
            ])->rules('required');
        })->when('products', function (Form $form) {
            $form->select('provider_type', __('Provider type'))->options([
                'Feed Supplier' => 'Feed supplier',
                'livestock Equipment' => 'Livestock equipment',
                'Farm Input Supplier' => 'Farm input supplier',
            ])->rules('required');
        })->when('drugs', function (Form $form) {
            $form->select('provider_type', __('Provider type'))->options([
                'Drug Shop' => 'Drug shop',
              
            ])->rules('required');
           
        });
        $form->text('name', __('Name'))->rules('required');
        $form->text('owner_name', __('Owner name'))->rules('required');
        $form->textarea('owner_profile', __('Owner profile'))->rules('required');
        $form->text('ursb_incorporation_number', __('Ursb incorporation number'))->rules('required');
        $form->date('date_of_incorporation', __('Date of incorporation'))->rules('required|before_or_equal:today');
        $form->text('type_of_shop', __('Type of shop(Agro/Vet)'))->rules('required');
        $form->date('date_of_registration', __('Date of registration'))->rules('required|before_or_equal:today');
        $form->text('physical_address', __('Physical address'))->rules('required');
        $form->text('primary_phone_number', __('Primary phone number'))->rules('required');
        $form->text('secondary_phone_number', __('Alternative phone number'));
        $form->email('email', __('Email'));
        $form->text('postal_address', __('Postal address'));
        $form->text('other_services', __('Other services'));
        $form->image('logo', __('Logo'));
        $form->text('district_of_operation', __('District of operation'))->help('District of operation - sub county')->rules('required');
        $form->text('tin_number_business', __('Tin number business'))->rules('required');
        $form->text('tin_number_owner', __('Tin number owner'));
        $form->file('NDA_registration_number', __('NDA registration'))->required();
        $form->file('license', __('License'))->required();
        $form->multipleFile('other_documents', __('Other documents'));
        $form->hidden('user_id');

        //check if the user is an admin and show the status field
        if (Admin::user()->inRoles(['administrator','ldf_admin'])) {
        $form->radioCard('status', __('Status'))->options(['halted' => 'Halted', 'approved' => 'Approved', 'rejected' => 'Rejected'])->rules('required');
        }

        return $form;
    }
}
