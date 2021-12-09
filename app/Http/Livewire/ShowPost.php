<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;

class ShowPost extends Component
{
    public $title;
    public $body;
    public $slug;
    public $image;
    public $author;

    public function mount($slug)
    {
        $this->retrievePost($slug);

    }

    public function retrievePost($slug)
    {
        $post = Post::where('slug',$slug)->first();
        $this->title = $post->title;
        $this->body  = $post->body;
        $this->image = $post->image;
        $this->author = $post->user->name;

    }


    public function return_to_posts()
    {
        return redirect()->to('posts');
    }

    public function render()
    {
        return view('livewire.show-post')->layout('layouts.app');
    }
}
