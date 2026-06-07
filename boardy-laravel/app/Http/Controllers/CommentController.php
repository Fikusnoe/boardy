<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
        public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'body'    => 'required|string|max:1000',
        ]);

	$data['author_name'] = $request->user()->name;

        $request->user()->comments()->create($data);

        return back()->with('success', 'Комментарий добавлен');
    }

}
