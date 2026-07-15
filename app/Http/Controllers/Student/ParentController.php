<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ParentController extends Controller
{
    // Liaison parent ↔ étudiant via parents_users, accès portail parent.
    public function index() : View{
        return view();
    }

    public function create() : View{
        return view();
    }

    public function store(){
        
    }
    
    public function show() : View{
        return view();
    }
    
    public function edit() : View{
        return view();
    }
    
    public function update(){
        
    }

    public function destroy(){
        
    }
}