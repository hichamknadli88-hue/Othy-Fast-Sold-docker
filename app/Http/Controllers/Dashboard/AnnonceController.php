<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AnnonceController extends Controller
{
public function index(): View|RedirectResponse
    {
        $annonces = Annonce::latest()->paginate(4)->fragment('gallery');

        // e.g. the last item of the last page was just deleted
        if ($annonces->isEmpty() && $annonces->currentPage() > 1) {
            return redirect()->route('admin.dashboard', ['page' => $annonces->lastPage()]);
        }

        $current = Annonce::chosen();

        return view('admin.dashboard', compact('annonces', 'current'));
    }
    // Upload from device AND set as annonce in one step
    public function store(Request $request): RedirectResponse
    {
        if(!Auth()->user()->isAdmin()) {
            return back()->with('error', 'ليس لديك صلاحية رفع صورة جديدة.');
        }
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ], [
            'image.required' => 'الرجاء اختيار صورة.',
            'image.uploaded' => 'فشل رفع الصورة، قد يكون حجمها أكبر من الحد المسموح.',
            'image.image'    => 'الملف المختار ليس صورة.',
            'image.mimes'    => 'الصيغ المسموحة: JPG, PNG, WEBP, GIF.',
            'image.max'      => 'الحد الأقصى لحجم الصورة هو 5 ميجابايت.',
        ]);

        $path    = $request->file('image')->store('annonces', 'public');
        $annonce = Annonce::create(['image_path' => $path]);
        $annonce->activate();

        return redirect()->route('admin.dashboard')->with('success', 'تم نشر الإعلان الجديد.');
    }

    // Pick a previously uploaded image as the annonce
    public function activate(Request $request, Annonce $annonce): RedirectResponse
    {
        $annonce->activate();

        return redirect()->route('admin.dashboard', array_filter(['page' => $request->input('page')]))
            ->with('success', 'تم تعيين الصورة كإعلان حالي.');
    }

    public function pause(Annonce $annonce): RedirectResponse
    {
        if (! $annonce->is_active) {
            return back()->with('error', 'هذه الصورة ليست الإعلان الحالي.');
        }

        $annonce->pause();

        return back()->with('success', 'تم إيقاف الإعلان مؤقتًا، ولن يظهر للزوار.');
    }

    public function resume(Annonce $annonce): RedirectResponse
    {
        if (! $annonce->is_active) {
            return back()->with('error', 'هذه الصورة ليست الإعلان الحالي.');
        }

        $annonce->resume();

        return back()->with('success', 'تم استئناف عرض الإعلان.');
    }

    // The current annonce can be deleted too: the site then simply has no annonce
    public function destroy(Annonce $annonce): RedirectResponse
    {
        Storage::disk('public')->delete($annonce->image_path);
        $annonce->delete();

        return back()->with('success', 'تم حذف الصورة.');
    }
}
