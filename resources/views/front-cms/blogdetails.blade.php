@extends('front-cms.layouts.main')
@section('main-section')

        <!-- END header -->
        <section>
            <div class="container-fluid">
                <img class="blogBanner" src="{{ url('images/blogs/'.$blog->banner) }}" height="600px" alt="">

                <div class="container">

                    
                    <div class="row my-5">
                        <div class="col-lg-8 col-md-8 col-sm-12 col-sm-12">
                            <div class="resource-left">
                                <h2>{{$blog->name}}</h2>
                            </div>
                        </div>
                        
                    </div>


                    <div class="row mb-5">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-sm-12">
                            <div class="resource-left">
                                {!! $blog->description !!}
                            </div>
                            <hr>
                        </div>
                       
                    </div>

                    <div class="row resourcesTop" style="margin:50px 0;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="resourcesBottom">
                                <h4>Featured</h4>
                                <h1 class="mb-3">Blogs</h1>
                                <button onclick="redirect();" class="btn search-tutor">Free Trial Class</button>
                            </div> 
                        </div>
                    </div>
                </div>
                <script>
                    function redirect(){
                        window.location.href = "{{('/student/register')}}";
                    }

                </script>
                


            </div>
        </div>
    </section>
  
@endsection
