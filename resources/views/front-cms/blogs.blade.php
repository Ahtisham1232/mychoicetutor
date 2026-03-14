@extends('front-cms.layouts.main')
@section('main-section')
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader">
                <h1 class="mb-3">
                    Explore Our Blogs
                </h1>

                <div class="text-center">
                    <p class="charcol">Stay informed with our expertly written articles.</p>

                </div>

            </div>
        </div>

    </section>
    <!-- tutor section -->
    <section class="mt-5">
        <div class="container ">
            <div class="resourcesTop">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="resourcesBottom">
                            <h4>Featured</h4>
                            <h1>Blogs</h1>
                            <br>
                            <br>
                            <div class="freeClassBtn">
                            <button class="btn search-tutor" onclick="redirect();">Free Trial Class</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
           

            <div class="row mt-5">
                @foreach ($blogs as $blog)
                
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="blog-card">
                        <img  src="{{ url('images/blogs/'.$blog->image) }}" width="100%" alt="">
                        <div class="blogDetails">
                            <span class="feature"><span>
                                <a href="resources/{{$blog->id}}"><h5 class="my-2" style="color: black">{{$blog->name}}</h5></a>
                                    <p class="bDesc">{!! Str::limit($blog->description, 150) !!}</p>
                                    <a href="{{ route('blog_details', $blog->id) }}">
                                        Read more
                                    </a>
                        </div>
                    </div>
                </div>
                @endforeach
                
            </div>

        <script> 
            function redirect(){
                window.location.href = "{{('/student/register')}}";
            }
        </script>

        </div>

    </section>
@endsection
