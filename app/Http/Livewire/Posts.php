<?php

namespace App\Http\Livewire;

use App\Helper\MySlugHelper;
use App\Models\Post;

use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Posts extends Component
{
    use WithPagination,WithFileUploads,LivewireAlert;

    public $title;
    public $body;
    public $image;
    public $post_image;
    public $image_name;
    public $slug_url;
    public $modal_id;

    public $modalFormVisible    = false;
    public $confirmPostDeletion = false;


    /**
     * return data array.
     * @return array
     */
    public function modalData()
    {
        $data =  [
            'title' => $this->title,
            'body'  => $this->body,
        ];

        if($this->post_image != '')
        {
            $data['image'] = $this->image_name;
        }

        return $data;
    }


    public function loadModalData()
    {
        $data = Post::findOrFail($this->modal_id);

        $this->title    = $data->title;
        $this->body     = $data->body;
        $this->slug_url = $data->slug;
        $this->image    = $data->image;

    }

    /**
     * reset Modal Form
     */
    public function modalFormReset()
    {
        $this->title      = null;
        $this->body       = null;
        $this->image      = null;
        $this->slug_url   = null;
        $this->image_name = null;
        $this->post_image = null;
        $this->modal_id   = null;

    }


    public function updatedTitle($value)
    {
        $this->slug_url = MySlugHelper::slug($value);
    }



    /**
     * get all posts from post table with pagination.
     * @return mixed
     */
    public function all_posts()
    {
       return Post::orderBy('id','desc')->paginate(5);
    }


    /**
     * Show Create Modal.
     */
    public function showCreateModal()
    {
        $this->emit('createNewPostEmit');
        $this->modalFormReset();
        $this->modalFormVisible = true;
    }


    /**
     * Show update Modal
     * @param $id
     */
    public function showUpdateModal($id)
    {
        $this->modalFormReset();
        $this->emit('updatePostEmit');
        $this->modalFormVisible = true;
        $this->modal_id = $id;
        $this->loadModalData();
    }


    public function showDeleteModal($id)
    {
        $this->confirmPostDeletion = true;
        $this->modal_id = $id;
    }



    /**
     * Set Validation Rules.
     * @return array
     */
    public function rules()
    {
        return [
            'title'         => ['required'],
            'slug_url'      => ['required',Rule::unique('posts','slug')->ignore($this->modal_id)],
            'body'          => ['required'],
            'post_image'    => [Rule::requiredIf(!$this->modal_id),'max:1024']
        ];
    }


    /**
     * Store Data In Database.
     */
    public function store()
    {
        $this->validate();

        if($this->post_image)
        {
            $this->image_name = md5($this->post_image . microtime()) . '.' . $this->post_image->extension();
            $this->post_image->storeAs('/', $this->image_name, 'uploads');
        }

        auth()->user()->posts()->create($this->modalData());

        $this->modalFormReset();

        $this->modalFormVisible = false;

        $this->alert('success', 'Post Created Successfully',[
            'position' => 'center',
            'timer' => 7000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }


    public function update()
    {
        $this->validate();

        $post = Post::where('id',$this->modal_id)->first();

        if($this->post_image != '')
        {
            if($post->image)
            {
                if(File::exists('images/'.$post->image))
                {
                    unlink('images/'.$post->image);
                }
            }

            $this->image_name = md5($this->post_image . microtime()) . '.' . $this->post_image->extension();
            $this->post_image->storeAs('/', $this->image_name, 'uploads');
        }

        $post->update($this->modalData());

        $this->modalFormVisible = false;

        $this->modalFormReset();

        $this->alert('success', 'Post Updated Successfully',[
            'position' => 'center',
            'timer' => 7000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);

    }



    public function destroy()
    {
        $post = Post::where('id',$this->modal_id)->first();

            if($post->image)
            {
                if(File::exists('images/'.$post->image))
                {
                    unlink('images/'.$post->image);
                }
            }

        $post->delete();

        $this->confirmPostDeletion = false;

        $this->resetPage();

        $this->alert('success', 'Post Deleted Successfully',[
            'position' => 'center',
            'timer' => 5000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);

    }


    public function render()
    {
        return view('livewire.posts',[
            'posts' => $this->all_posts()
        ]);
    }


}
