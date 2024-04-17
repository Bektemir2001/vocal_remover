@extends('layouts.app')
@section('content')
    <div class="col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Add sound</h4>
                </div>
                <div class="header-action">
                    <i data-toggle="collapse" data-target="#form-element-1" aria-expanded="false">
                        <svg width="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </i>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse" id="form-element-1">
                    <div class="card"></div>
                </div>
                <form action="{{route('audios.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="pwd">Type</label>
                        <select class="form-control" onclick="detectType(this)">
                            <option value="audio">audio</option>
                            <option value="youtube">YouTube link</option>
                        </select>
                    </div>
                    <div class="form-group" id="TypeId">
                        <label for="audio">Audio</label>
                        <input type="file" class="form-control" id="audio" name="audio" required>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <a href="{{route('audios.index')}}" class="btn bg-danger">Cancel</a>
                </form>
            </div>
        </div>
    </div>


    <script>
        function detectType(select)
        {
            let div_content = document.getElementById('TypeId');
            if(select.value === 'audio')
            {
                div_content.innerHTML = `
                    <label for="audio">Audio</label>
                     <input type="file" class="form-control" id="audio" name="audio" required>
                `;
            }
            else{
                div_content.innerHTML = `
                    <label for="audio">YouTube link</label>
                     <input type="text" class="form-control" id="youtube_link" name="youtube_link" required>
                `;
            }
        }
    </script>
@endsection
