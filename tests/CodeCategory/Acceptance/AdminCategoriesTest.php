<?php

namespace CodePress\CodeCategory\Testing;

use CodePress\CodeCategory\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminCategoriesTest extends \TestCase
{
	use DatabaseTransactions;

	public function test_can_visist_admin_categories_page()
	{
		$this->visit('admin/categories')
		     ->see('Categories');
	}

	public function test_verity_categories_listing()
	{
		Category::create(['name'=>'Category 1', 'active'=>true]);
		Category::create(['name'=>'Category 2', 'active'=>true]);
		Category::create(['name'=>'Category 3', 'active'=>true]);
		Category::create(['name'=>'Category 4', 'active'=>true]);

		$this->visit('/admin/categories')
		     ->see('Category 1')
		     ->see('Category 2')
		     ->see('Category 3')
		     ->see('Category 4');
	}

	public function test_click_create_new_category()
	{
		$this->visit('/admin/categories')
		     ->click('Create Category')
		     ->seePageIs('/admin/categories/create')
		     ->see('Create Category');
	}

	public function test_create_new_category()
	{
		$this->visit('/admin/categories/create')
		     ->type('Category Test', 'name')
		     ->check('active')
		     ->press('Submit')
		     ->seePageIs('admin/categories')
		     ->see('Category Test');
	}

	public function test_click_edit_category_with_parent()
	{
		$categoryParent = Category::create(['name'=>'Jose', 'active'=>true]);
		$category = Category::create(['name'=>'Léo', 'active'=>true]);
		$category->parent()->associate($categoryParent)->save();

		$this->visit('/admin/categories')
		     ->seeLink('Edit', route('admin.categories.edit', ['id'=>$category->id]))
		     ->get(route('admin.categories.edit', ['id'=>$category->id]))
		     ->seePageIs(route('admin.categories.edit', ['id'=>$category->id]))
		     ->see('Edit Category')
		     ->see($category->parent->name)
		     ->see($category->name)
		     ->see($category->active);
	}

	public function test_click_edit_category_without_parent()
	{
		$category = Category::create(['name'=>'Léo', 'active'=>true]);

		$this->visit('/admin/categories')
		     ->seeLink('Edit', route('admin.categories.edit', ['id'=>$category->id]))
		     ->get(route('admin.categories.edit', ['id'=>$category->id]))
		     ->seePageIs(route('admin.categories.edit', ['id'=>$category->id]))
		     ->see('Edit Category')
		     ->see(null)
		     ->see($category->name)
		     ->see($category->active);
	}

	public function test_click_update_category()
	{
		$category = Category::create(['name'=>'Léo', 'active'=>true]);

		$this->visit(route('admin.categories.edit', ['id'=>$category->id]))
			->type('Category Test', 'name')
		    ->check('active')
		    ->press('Submit')
		    ->seePageIs('admin/categories')
		    ->see('Category Test');
	}

	public function test_click_delete_category()
	{
		$category = Category::create(['name'=>'Léo', 'active'=>true]);

		$this->visit('/admin/categories/create')
		     ->type($category->name, 'name')
		     ->check('active')
		     ->press('Submit')
		     ->seePageIs('admin/categories')
		     ->see('Léo')
		     ->seeLink('Delete', route('admin.categories.destroy', ['id'=>$category->id]))
		     ->get(route('admin.categories.destroy', ['id'=>$category->id]))
		     ->assertRedirectedTo('admin/categories')
		     ->dontSeeText($category->name);		     
	}

}