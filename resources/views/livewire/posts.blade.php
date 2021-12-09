<div>
   <div class="flex item-center  px-4 py-4">
       <x-jet-button wire:click="showCreateModal">
           {{__('Create_post')}}
       </x-jet-button>
   </div>

    <table class="w-full divide-y divide-grey-200">
        <thead>
        <tr>
            <th class="px-6 py-3 border-b-2 border-grey-200 text-left text-blue-500 tracking-wider">#</th>
            <th class="px-6 py-3 border-b-2 border-grey-200 text-left text-blue-500 tracking-wider">{{__('Image')}}</th>
            <th class="px-6 py-3 border-b-2 border-grey-200 text-left text-blue-500 tracking-wider">{{__('Title')}}</th>
            <th class="px-6 py-3 border-b-2 border-grey-200 text-left text-blue-500 tracking-wider">{{__('Created_by')}}</th>
            <th class="px-6 py-3 border-b-2 border-grey-200 text-left text-blue-500 tracking-wider">{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-grey-200">
        @forelse($posts as $post)
        <tr>
            <td class="px-6 py-3 border-b border-grey-200">{{$post->id}}</td>
            <td class="px-6 py-3 border-b border-grey-200"><img src="{{asset('images/'.$post->image)}}" alt="{{$post->title}}" width="80"></td>
            <td class="px-6 py-3 border-b border-grey-200">
                <a href="{{route('show_post',$post->slug)}}">{{$post->title}}</a>
            </td>
            <td class="px-6 py-3 border-b border-grey-200">{{$post->user->name}}</td>
            <td class="px-6 py-3 border-b border-grey-200">

                <div class="flex item-center  px-4 py-4">
                    <x-jet-button class="mr-2" wire:click="showUpdateModal({{$post->id}})">
                        {{__('Edit')}}
                    </x-jet-button>

                    <x-jet-danger-button wire:click="showDeleteModal({{$post->id}})">
                        {{__('Delete')}}
                    </x-jet-danger-button>
                </div>

            </td>
        </tr>
        @empty
            <td class="px-6 py-3 border-b border-grey-200 text-center" colspan="5">No Posts Found.</td>
        @endforelse
        </tbody>
    </table>

    <div class="pt-4">
        {{$posts->links()}}
    </div>

    <x-jet-dialog-modal wire:model="modalFormVisible">

        <x-slot name="title">
            {{ $modal_id ? __('Update Post') : __('Create Post')}}
        </x-slot>

        <x-slot name="content">

           <div class="mt-4">
               <x-jet-label for="title" value="{{__('Post Title')}}"></x-jet-label>
               <x-jet-input type="text" id="title" wire:model.debounce.500ms="title" class="block mt-2 w-full"></x-jet-input>
               @error('title')
               <span class="text-red-900 text-sm font-extrabold">{{$message}}</span>
               @enderror
           </div>

            <div class="mt-4">
                <x-jet-label for="slug" value="{{__('Slug')}}"></x-jet-label>
                <div class="mt-2 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        {{config('app.url').'/'}}
                    </span>
                    <x-jet-input type="text" id="slug" wire:model="slug_url" class="block w-full form-input flex-1 rounded-none rounded-r-md transition duration-150 ease-in-out sm:text-sm sm:leading-5" placeholder="url slug"></x-jet-input>
                </div>

                @error('slug')
                <span class="text-red-900 text-sm font-extrabold">{{$message}}</span>
                @enderror

            </div>


            <div class="mt-4">
                <x-jet-label for="body" value="{{__('Post Content')}}"></x-jet-label>

             <div wire:ignore wire:key="myBody">
                 <div id="body" class="block mt-2 w-full">
                     {{$body}}
                 </div>
             </div>

                <textarea id="body" class="hidden body-content" wire:model.debounce.2000="body">{{$body}}</textarea>

                @error('body')
                <span class="text-red-900 text-sm font-extrabold">{{$message}}</span>
                @enderror

            </div>

            <div class="mt-4">
                <x-jet-label for="image" value="{{__('Post Image')}}"></x-jet-label>

                <div class="flex py-3">

                        @if($image)
                            <div class="mt-2 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center p-3 rounded border border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        <img src="{{asset('images/'.$image)}}" width="200">
                                    </span>
                            </div>
                        @endif

                </div>

                <div class="flex py-3">

                    @if($post_image)
                        <div class="mt-2 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center p-3 rounded border border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    <img src="{{$post_image->temporaryUrl()}}" width="200">
                                </span>
                        </div>
                        <br>
                    @endif
                </div>

                <input type="file" id="image" wire:model="post_image" class="form-input flex-1 block w-full rounded-none rounded-r-md transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                @error('post_image')
                <span class="text-red-900 text-sm font-extrabold">{{$message}}</span>
                @enderror
            </div>


        </x-slot>

        <x-slot name="footer">

            @if($modal_id)
                 <x-jet-button wire:click="update">{{__('Update Post')}}</x-jet-button>
            @else
                <x-jet-button wire:click="store">{{__('Create Post')}}</x-jet-button>
            @endif
            <x-jet-secondary-button wire:click="$toggle('modalFormVisible')" class="ml-2">{{__('Cancel')}}</x-jet-secondary-button>
        </x-slot>

    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="confirmPostDeletion">

        <x-slot name="title">
            {{ __('Delete Post')}}
        </x-slot>

        <x-slot name="content">
            {{__('Are You Sure To Delete This Post ? ')}}
        </x-slot>

        <x-slot name="footer">

            <x-jet-danger-button wire:click="destroy">{{__('Delete Post')}}</x-jet-danger-button>

            <x-jet-secondary-button wire:click="$toggle('confirmPostDeletion')" class="ml-2">{{__('Cancel')}}</x-jet-secondary-button>
        </x-slot>

    </x-jet-dialog-modal>

</div>

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/31.0.0/classic/ckeditor.js"></script>
    <script>


      window.onload = function (){
          if(document.querySelector('#body'))
          {
              ClassicEditor.
                  create( document.querySelector( '#body' ) ,{})
                  .then( editor => {
                      editor.model.document.on('change:data',() => {

                          document.querySelector('#body').value = editor.getData();
                          @this.set('body',document.querySelector('#body').value);

                      });

                      Livewire.on('updatePostEmit',function (){

                          editor.setData(document.querySelector('.body-content').value)
                      });

                      Livewire.on('createNewPostEmit',function (){

                          editor.setData('')
                      });



                  } )
                  .catch( error => {
                      console.error( error.stack );
                  } );
          }

      }
    </script>
@endpush
