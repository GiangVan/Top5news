<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Poster;
use App\User;
use App\Category;

class PosterManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function showEditingPage($id){
        $poster = $this->checkPosterExist($id);
        if($poster !== false){
            $category = Category::all();
            return view('poster/edit', compact('poster', 'category'));
        }
        else
        {
            return view('redirect', ['url' => '/admin']);
        }
	}

	public function approve($id){
		if(Auth::user()->role === 'admin'){
			$poster = Poster::find($id);
			$poster->id_approver = Auth::id();
			if($poster->save()){
				return view('redirect', ['url' => '/admin']);
			}
		}
	}


	public function showAll()
	{
		$posters = DB::table('categories')->join('posters', 'posters.id_category', '=', 'categories.id')->join('users', 'users.id', '=', 'posters.id_creator')->where('users.id', '=', Auth::id())->get(['categories.title as categorytitle', 'posters.id as poster_id', 'categories.id as category_id', 'users.*', 'posters.*']);
		return view('myposts', compact('posters'));
	}

    public function delete($id){
		if(Auth::user()->role === 'admin'){
			Poster::find($id)->delete();
			return view('redirect', ['url' => '/myposts']);
		} else {
			$poster = Poster::find($id);
			if($poster && $poster->id_creator === Auth::id()){
				$poster->delete();
				return view('redirect', ['url' => '/myposts']);
			}
		}
    }

    public function showAddingPage(){
        $category = Category::all();
        return view('poster/add', compact('category'));
    }

    public function add(Request $request){
        $poster = new Poster;
        $poster->title = $request->title;
        $poster->content = $request->content;
        $poster->id_category = $request->category;
        $poster->id_creator = Auth::id();

        $poster->save();

        return view('redirect', ['url' => '/myposts']);
    }

    public function edit(Request $request){
        if($this->checkPosterExist($request->id) !== false){
            $poster = Poster::find($request->id);
            $poster->title = $request->title;
            $poster->content = $request->content;
            $poster->id_category = $request->category;
            $poster->save();
        }
        return view('redirect', ['url' => '/myposts']);
    }

    public function apiget(Request $request){
        $id = $request->id;
        return "hello {$id}";
    }

    private function checkPosterExist($id){
        if($id !== '')
        {
            $posters = $posters = DB::table('categories')->join('posters', 'posters.id_category', '=', 'categories.id')->join('users', 'users.id', '=', 'posters.id_creator')->where('users.id', '=', Auth::id())->where('posters.id', '=', $id)->get(['categories.title as categorytitle', 'posters.id as poster_id', 'categories.id as category_id', 'users.*', 'posters.*']);
            if(count($posters))
            {
                $poster = $posters->where('id', $id);
                if(count($poster)){
                    foreach ($poster as $value) {
                        $poster = $value;
                    }
                    return $poster;
                }
            }
        }
        return false;
    }
}
