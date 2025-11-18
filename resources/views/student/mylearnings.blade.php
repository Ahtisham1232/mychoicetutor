@extends('student.layouts.main')
@section('main-section')
<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
            <style>
                .listHeader {
                    display: flex;
                    justify-content: space-between;
                }
            </style>

                    <div id="listHeader">
                        <h3>My Learning</h3>

                    </div>

                <!-- content-wrapper ends -->


                    <form action="{{route('student.mylearnings')}}" method="post">
                        @csrf
                        <div class="row ">
                            <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12 mt-4">
                                <input type="text" value="{{$requests['topic'] ?? ''}}" name="topic" class="form-control" placeholder="Enter Topic">
                            </div>



                            <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12 mt-4" >
                                <div class="myLearningSearch">
                                    <button class="btn  btn-primary"> <span
                                    class="fa fa-search"></span> Search</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                            <thead >
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Topic</th>
                                    <th scope="col">Study Content</th>
                                    <th scope="col">Videos</th>
                                    <th scope="col">Blogs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($learnings as $learning)


                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$learning->topic_name}}</td>
                                    @if ($learning->content_description)


                                    <td class="text-wrap"><div class="text-center">{{$learning->content_description}}&nbsp;
                                        @if ($learning->content_link)
                                            <a class="badge bg-primary" href="{{url('uploads/documents/learningcontents')}}/{{$learning->content_link}}" target="_blank"><b>View</b></a>
                                        @endif
                                    </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif

                                    @if($learning->video_description)
                                    <td class="text-wrap"><div class="text-center">{{$learning->video_description}}&nbsp;
                                        @if ($learning->video_link)
                                            <a class="badge bg-primary" href="{{url('uploads/videos/learningcontents')}}/{{$learning->video_link}}" target="_blank"><b>View</b></a>
                                        @endif
                                    </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                    @if ($learning->blog_description)

                                    <td class="text-wrap"><div class="text-center">{{$learning->blog_description}}&nbsp;
                                        <a class="badge bg-primary" href="{{$learning->blog_link}}"><b>View</b></a>
                                        {{-- <a class="badge bg-primary" data-toggle="modal" data-target="#popUpVideoModal"><b>View</b></a> --}}
                                    </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {!! $learnings->appends(['topic' => $requests['topic'] ?? ''])->links() !!}
                    </div>
                </div>
            </div>
         </div>
                <!-- content-wrapper ends -->


    <div class="modal fade" id="popUpVideoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="text-center mt-3">
                    <h5 class="modal-title" id="exampleModalLabel"> Sample Video</h5>
                </div>
                <div class="modal-body">
                    <iframe width="100%" height="300px" src="https://www.youtube.com/embed/n5FNuytDFFE"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


                </div>
                <!-- <div class="modal-body">
                            <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
                        </div> -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="studyMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="text-center mt-3">
                    <h5 class="modal-title" id="exampleModalLabel">Study Content</h5>
                </div>
                <div class="modal-body">
                    <iframe width="100%" height="500px"
                        src="studyMaterial/10_maths_key_notes_ch_01_real_numbers.pdf"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


                </div>
                <!-- <div class="modal-body">
                                <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
                            </div> -->
            </div>
        </div>
    </div>

@endsection
