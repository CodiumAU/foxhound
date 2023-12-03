@extends('foxhound::template')

@section('content')
<div class="h-full flex items-center justify-center min-h-[700px]">
    <figure class="w-[320px] h-[650px]">
        <div
            class="p-1.5 h-full bg-slate-800 rounded-3xl shadow-[0_2.75rem_5.5rem_-3.5rem_rgb(45_55_75_/_20%),_0_2rem_4rem_-2rem_rgb(45_55_75_/_30%),_inset_0_-0.1875rem_0.3125rem_0_rgb(45_55_75_/_20%)] dark:bg-gray-600 dark:shadow-[0_2.75rem_5.5rem_-3.5rem_rgb(0_0_0_/_20%),_0_2rem_4rem_-2rem_rgb(0_0_0_/_30%),_inset_0_-0.1875rem_0.3125rem_0_rgb(0_0_0_/_20%)]"
        >
            <div class="w-full h-full bg-white rounded-[1.25rem] p-3">
                <div class="rounded-[1.25rem] bg-sky-500 text-white p-4">
                    {{ $message }}
                </div>
            </div>
        </div>
    </figure>
</div>
@endsection

