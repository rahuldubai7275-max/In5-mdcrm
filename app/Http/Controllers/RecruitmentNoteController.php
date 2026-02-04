<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\RecruitmentNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecruitmentNoteController extends Controller
{
    public function Store(Request $request){
        $request->validate([
            'NoteSubject'=>'required',
            'recruitment'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $note_subject=request('NoteSubject');
        $recruitment_id=request('recruitment');

        RecruitmentNote::create([
            'admin_id'=>$adminAuth->id,
            'recruitment_id'=>$recruitment_id,
            'note_subject'=>$note_subject,
            'date_at'=>request('DateAt'),
            'time_at'=>request('TimeAt'),
            'note'=>request('Note')
        ]);

        return redirect('/admin/recruitment-view/'.$recruitment_id);

    }
}
