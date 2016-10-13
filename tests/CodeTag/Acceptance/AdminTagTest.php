<?php

namespace CodePress\CodeTag\Testing;

use CodePress\CodeTag\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTagsTest extends \TestCase
{
	use DatabaseTransactions;

	public function test_can_visist_admin_tags_page()
	{
		$this->visit('admin/tags')
		     ->see('Tags');
	}

	public function test_verify_tags_listing()
	{
		Tag::create(['name'=>'Tag 1']);
		Tag::create(['name'=>'Tag 2']);
		Tag::create(['name'=>'Tag 3']);
		Tag::create(['name'=>'Tag 4']);

		$this->visit('/admin/tags')
			->see('Tag 1')
			->see('Tag 2')
			->see('Tag 3')
			->see('Tag 4');
	}

	public function test_click_create_new_tag()
	{
		$this->visit('/admin/tags')
			->click('Create Tag')
			->seePageIs('/admin/tags/create')
			->see('Create Tag');
	}

	public function test_create_new_tag()
	{
		$this->visit('/admin/tags/create')
			->type('Tag Test', 'name')
			->press('Submit')
			->seePageIs('/admin/tags')
			->see('Tag Test');
	}

	public function test_click_edit_tag()
	{
		$tag = Tag::create(['name'=>'Tag Admin']);

		$this->visit('/admin/tags')
			->seeLink('Edit', route('admin.tags.edit', ['id'=>$tag->id]))
			->get(route('admin.tags.edit', ['id'=>$tag->id]))
			->seePageIs(route('admin.tags.edit', ['id'=>$tag->id]))
			->see('Edit Tag')
			->see($tag->name);
	}

	public function test_click_update_tag()
	{
		$tag = Tag::create(['name'=>'Tag 1']);

		$this->visit(route('admin.tags.edit', ['id'=>$tag->id]))
			->type('Tag Admin', 'name')
			->press('Submit')
			->seePageIs('/admin/tags')
			->see('Tag Admin');
	}

	public function test_click_delete_tag()
	{
		$tag = Tag::create(['name'=>'Tag 1']);

		$this->visit('/admin/tags/create')
			->type($tag->name, 'name')
			->press('Submit')
			->seePageIs('/admin/tags')
			->see('Tag 1')
			->seeLink('Delete', route('admin.tags.destroy', ['id'=>$tag->id]))
			->get(route('admin.tags.destroy', ['id'=>$tag->id]))
			->assertRedirectedTo('/admin/tags')
			->dontSeeText($tag->id);
	}

}