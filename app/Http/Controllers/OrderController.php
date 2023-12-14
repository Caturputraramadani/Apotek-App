<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\OrderExport;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->simplePaginate(10);
        return view('order.kasir.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view('order.kasir.create', compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_customer' => 'required',
            'medicines' => 'required',
        ]);
        // mencari jumlah item yg sama pada array, strukturnya :
        // ["item" => "jumlah"]
        $arrayDistinct = array_count_values($request->medicines);
        // menyiapkan array kosong untuk menampung format array baru
        $arrayAssocMedicines = [];
        // looping hasil perhitungan item distinct (duplikat)
        // key akan berupa value dr input medicines (id), item array berupa jumlah perhitungan item duplikat
        foreach ($arrayDistinct as $id => $count) {
            // mencari data obat berdasarkan id (obat yg dipilih)
            $medicine = Medicine::where('id', $id)->first();
            // ambil bagian column price dr hasil pencarian lalu kalikan dengan jumlah item duplikat sehingga akan menghasilkan total harga dr pembelian obat tsb
            $subPrice = $medicine['price'] * $count;
            //  struktur value olumn medicine menjadi multidimensi dgn dimensi kedua berbentuk array assoc dengan key "id", "name_medicine", "qty","price"
            $arrayItem = [
                "id" => $id,
                "name_medicine" => $medicine["name"],
                "qty" => $count,
                "price" => $medicine['price'],
                "sub_price" => $subPrice,
            ];
            // masukkan struktur array tsb ke array kosong yg disediakan sebelumnya 
            array_push($arrayAssocMedicines, $arrayItem);
        }
        // total harga pembelian dari obat yang dipilih
        $totalPrice = 0;
        // looping format array medicines baru 
        foreach ($arrayAssocMedicines as $item) {
            // total harga pembelian ditambahkan dr keseluruhan sub_price data medicines
            $totalPrice += (int)$item['sub_price'];
        }
        // harga beli ditambah 10% ppn
        $priceWithPPN = $totalPrice + ($totalPrice * 0.01);
        // tambah data ke database
        $proses = Order::create([
            // data user_id diambil dari id akun ksir yg sedang login
            'user_id' => Auth::user()->id,
            'medicines' => $arrayAssocMedicines,
            'name_customer' => $request->name_customer,
            'total_price' => $priceWithPPN,
        ]);

        if ($proses) {
            // jika proses tambah data berhasil, ambil data order yg dibuat oleh kasir yg sedang login (where), dng tanggal paling terbaru (orderBy), ambil hanya satu data (first)
            $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();
            // kirim data order yg diambil td, bagian column id sebagai parameter path dari route print
            return redirect()->route('kasir.order.print', $order['id']);
        } else {
            // jika tidak berhasil, maka diarahkan kembali ke halaman form dgn pesan pemberitahuan
            return redirect()->back()->with('failed', 'Gagal membuat data pembelian, Silahkan coba kembali dengan data yang sesuai!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::find($id);
        return view('order.kasir.print', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function downloadPDF($id)
    {
        // ambil data yg diperlukan, dan pastikan data berformat array
        $order = Order::find($id)->toArray();
        // mengirim inisial variable dari data yg akan digunakan pada layout pdf
        view()->share('order', $order);
        // panggil blade yang akan di didownload
        $pdf = PDF::loadView('order.kasir.download-pdf', $order);
        // kembalikan atau hasilkan bentuk pdf dengan nama file tertentu
        return $pdf->download('receipt.pdf');
    }
    public function filterByDate(Request $request)
    {
        // Ambil data tanggal dari input form
        $tanggalPembelian = $request->input('tanggal_pembelian');

        // Jika tanggal tidak kosong, lakukan pencarian berdasarkan tanggal
        if ($tanggalPembelian) {
            // Konversi tanggal ke format yang sesuai untuk query database (misalnya format YYYY-MM-DD)
            $formattedDate = date('Y-m-d', strtotime($tanggalPembelian));

            // Lakukan pencarian data berdasarkan tanggal pembelian
            $orders = Order::with('user')
                ->whereDate('created_at', $formattedDate)
                ->simplePaginate(10);

            // Kembalikan hasil pencarian ke halaman view dengan parameter $orders
            return view('order.kasir.index', compact('orders'));
        } else {
            // Jika tanggal kosong, kembalikan ke halaman index tanpa filter
            return redirect()->route('kasir.order.index');
        }
    }
    public function adminFilterByDate(Request $request)
    {
        // Ambil data tanggal dari input form
        $tanggalPembelian = $request->input('tanggal_pembelian');

        // Jika tanggal tidak kosong, lakukan pencarian berdasarkan tanggal
        if ($tanggalPembelian) {
            // Konversi tanggal ke format yang sesuai untuk query database (misalnya format YYYY-MM-DD)
            $formattedDate = date('Y-m-d', strtotime($tanggalPembelian));

            // Lakukan pencarian data berdasarkan tanggal pembelian
            $orders = Order::with('user')
            ->whereDate('created_at', $formattedDate)
                ->simplePaginate(10);

            // Kembalikan hasil pencarian ke halaman view dengan parameter $orders
            return view('order.admin.index', compact('orders'));
        } else {
            // Jika tanggal kosong, kembalikan ke halaman index tanpa filter
            return redirect()->route('admin.order.data');
        }
    }



    public function data()
    {
        // with: mengambil hasil relasi dari PK dan Fk. valuenya == nama func relasi hasMany/BelongsTo
        $orders = Order::with('user')->simplePaginate(5);
        return view("order.admin.index", compact('orders'));
    }

    public function exportExcel()
    {
        $file_name = 'data_pembelian' . '.xlsx';
        return Excel::download(new OrderExport, $file_name);
    }
    public function adminData()
    {
        $orders = Order::with('user')->simplePaginate(5);
        return view("order.admin.index", compact('orders'));
    }

    public function adminDownloadPDF($id)
    {
        $order = Order::find($id)->toArray();
        view()->share('order', $order);
        $pdf = PDF::loadView('order.admin.download-pdf', $order);
        return $pdf->download('admin_receipt.pdf');
    }
}
