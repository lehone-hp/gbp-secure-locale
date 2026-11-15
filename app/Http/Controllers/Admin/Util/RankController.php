<?php

namespace App\Http\Controllers\Admin\Util;

use App\Contracts\ModelRanking\HasRanking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    public function updateRank(Request $request)
    {
        $request->validate([
            'class'     => 'required|string',
            'id'        => 'required|integer',
            'rank'      => 'required|integer|min:1',
        ]);

        $class = app()->make("App\\Models\\{$request->get('class')}");
        $member = $class::findOrFail($request->get('id'));

        if (!($member instanceof HasRanking)) {
            abort(422, 'Class must implement HasRanking');
        }

        $newRank = $request->get('rank');
        $oldMember = $class::orderBy('rank', 'DESC')->first();

        if ($newRank < 1 || ($oldMember && $newRank > $oldMember->rank)) {
            abort(422, 'rank must be greater than 1 ' . $oldMember ? ' and less than ' . $oldMember->rank : '');
        }

        try {
            DB::transaction(function () use ($member, $newRank, $class) {
                $old_rank = $member->rank;
                if ($old_rank != $newRank) {
                    $member->rank = 0;
                    $member->save();

                    // placing the item at the top half or list
                    if ($old_rank > $newRank) {
                        foreach ($class::where('rank', '<', $old_rank)
                                     ->where('rank', '>=', $newRank)
                                     ->orderBy('rank', 'DESC')->get() as $p) {
                            $p->rank = $p->rank + 1;
                            $p->save();
                        }
                    } else {
                        // placing the item at the bottom half or list
                        foreach ($class::where('rank', '>', $old_rank)
                                     ->where('rank', '<=', $newRank)
                                     ->orderBy('rank', 'ASC')->get() as $p) {
                            $p->rank = $p->rank - 1;
                            $p->save();
                        }
                    }

                    $member->rank = $newRank;
                    $member->save();
                }
            });
        } catch (\Exception $e) {
            abort(500);
        }

        return response()->json(['status' => 'success']);
    }
}
