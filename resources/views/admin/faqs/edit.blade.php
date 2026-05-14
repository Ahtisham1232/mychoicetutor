@extends('admin.layouts.main')
@section('main-section')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update FAQ</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.faqs.store') }}" method="POST">
                            @csrf
                            {{-- Hidden ID field so the controller knows to Update instead of Create --}}
                            <input type="hidden" name="id" value="{{ $faq->id }}">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="question" class="form-label">Question</label>
                                    <input type="text" name="question" id="question"
                                        class="form-control @error('question') is-invalid @enderror"
                                        value="{{ old('question', $faq->question) }}" required>
                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="answer" class="form-label">Answer</label>
                                    <textarea name="answer" id="editor1" rows="10" class="form-control @error('answer') is-invalid @enderror"
                                        required>{{ old('answer', $faq->answer) }}</textarea>
                                    @error('answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update FAQ</button>
                                <a href="{{ route('admin.faqs.list') }}" class="btn btn-primary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
