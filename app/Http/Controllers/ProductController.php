<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Check;
use App\Models\Cloth;
use App\Models\Company;
use App\Models\Computer;
use App\Models\Coupon;
use App\Models\CouponGift;
use App\Models\Electrical;
use App\Models\Like;
use App\Models\Mobile;
use App\Models\Notification;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\User;
use App\Models\View;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{


    public function index()
    {
        $products = Product::query()
            ->where('booked', '=', false)
            ->where('quantity', '=', 1)
            ->with('account', 'category', 'comments')
            ->get();
        return response()->json($products, Response::HTTP_OK);
    }


    public function store(Request $request, $type_product)
    {
        $validator = Validator::make($request->all(), [
            'name_of_the_company' => ['required', 'string', 'min:3'],
            'type_of_the_company' => ['required', 'string', 'min:3'],
            'color' => ['required', 'string', 'min:3'],

            'name' => ['string', 'min:3'],
            'details' => ['string', 'min:50'],

            'ram' => ['numeric'],
            'memory' => ['numeric'],
            'front_camera' => ['numeric'],
            'back_camera' => ['numeric'],

            'graphics' => ['numeric'],
            'cpu' => ['string', 'min:3'],

            'size' => ['string', 'min:3'],
            'gender' => ['string', 'min:3'],

            'quantity' => ['required', 'numeric'],
            'price' => ['required', 'numeric', 'max:9000000000'],
            'date_of_production' => ['date', 'required'],
            'new_or_old' => ['required', 'string', 'max:3'],
            'discount' => ['numeric'],
            'numberId' => ['numeric', 'min:11', 'max:10011000000'],
            'location' => ['string']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $categories = Category::query()->create([
            'name_of_the_company' => $request->name_of_the_company,
            'type_of_the_company' => $request->type_of_the_company,
            'color' => $request->color,
        ]);

        if ($type_product == 'electrical') {
            Electrical::query()->create([
                'category_id' => $categories->id,
                'name' => $request->name,
                'details' => $request->details,
            ]);
        } else if ($type_product == 'mobile') {
            Mobile::query()->create([
                'category_id' => $categories->id,
                'ram' => $request->ram,
                'memory' => $request->memory,
                'front_camera' => $request->front_camera,
                'back_camera' => $request->back_camera,

            ]);
        } else if ($type_product == 'computer') {
            Computer::query()->create([
                'category_id' => $categories->id,
                'ram' => $request->ram,
                'memory' => $request->memory,
                'graphics' => $request->graphics,
                'cpu' => $request->cpu,

            ]);
        } else if ($type_product == 'cloth') {
            Cloth::query()->create([
                'category_id' => $categories->id,
                'type' => $request->type,
                'size' => $request->size,
                'gender' => $request->gender,
            ]);
        }

        $products = Product::query()->create([
            'category_id' => $categories->id,
            'account_id' => Auth::id(),
//            'account_id' => $request->account_id,
            'name_of_product' => $request->name_of_product,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'date_of_production' => $request->date_of_production,
            'new_or_old' => $request->new_or_old,
            'discount' => $request->discount,
            'booked_at'=>$request->booked_at,
            'buy_at'=>$request->buy_at
        ]);

        Notification::query()
            ->create([
                'account_id' => $products->account_id,
                'content' => 'تم اضافة منتج للبيع من قبلك'
            ]);

        Check::query()
            ->create([
                'account_id' => Auth::id(),
                'numberId' => $request->numberId,
                'location' => $request->location
            ]);

        return response()->json($products->fresh());
//        return response()->json(['message' => 'The Product was Created Successfully.']);
    }


    public function show($product_id)
    {
        $product = Product::query()
            ->with('account', 'category', 'comments')
            ->find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        $account = Account::query()->find($product->account_id);
        $computer = Computer::query()->where('category_id', $product->category_id)->get();
        $cloth = Cloth::query()->find($product->category_id);
        $mobile = Mobile::query()->find($product->category_id);
        $electrical = Electrical::query()->find($product->category_id);
        $user = User::query()->find($account->owner_id);
        $company = Company::query()->find($account->owner_id);

        $products = ([]);

        if ($computer) {
            $products = ([
                'product' => $product,
                'computer' => $computer
            ]);
        } elseif ($cloth) {
            $products = ([
                'product' => $product,
                'cloth' => $cloth
            ]);
        } elseif ($mobile) {
            $products = ([
                'product' => $product,
                'mobile' => $mobile
            ]);
        } elseif ($electrical) {
            $products = ([
                'product' => $product,
                'electrical' => $electrical
            ]);
        }
        if ($user) {
            $products = ([
                'details' => $products,
                'user' => $user,
            ]);
        } elseif ($company) {
            $products = ([
                'details' => $products,
                'company' => $company
            ]);
        }

        $product->withCount;

        $maxDiscount = $product->discount;
        if (!is_null($maxDiscount)) {
            $discount_value = ($product->price * $maxDiscount) / 100;
            $product['current_price'] = $product->price - $discount_value;
        }

        $product->makeHidden('id', 'account_id', 'category_id', 'created_at', 'updated_at');
        $product->category->makeHidden('id', 'user_id', 'created_at', 'updated_at');
        $product->account->makeHidden('id', 'password', 'created_at', 'updated_at', 'owner_id', 'owner_type');


        $view = View::query()
            ->where('account_id', Auth::id())
            ->where('product_id', $product_id)
            ->first();

        if (!$view) {
            View::query()->create([
                'account_id' => Auth::id(),
                'product_id' => $product_id
            ]);
            $product->increment('viewer');
        }
//        Product::query()->find($product_id)->increment('viewers');
        return response()->json($products, Response::HTTP_OK);

    }


    public function update(Request $request, $product_id)
    {
        $product = Product::query()->find($product_id);
        $category = Category::query()->find($product->category_id);
        $computer = Computer::query()->find($product->category_id);
        $cloth = Cloth::query()->find($product->category_id);
        $mobile = Mobile::query()->find($product->category_id);
        $electrical = Electrical::query()->find($product->category_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }
        if (Auth::id() == $product->account_id) {
            $validator = Validator::make($request->all(), [
                'name_of_the_company' => ['string', 'min:3'],
                'type_of_the_company' => ['string', 'min:3'],
                'color' => ['string', 'min:3'],

                'name' => ['string', 'min:3'],
                'details' => ['string', 'min:50'],

                'ram' => ['numeric'],
                'memory' => ['numeric'],
                'front_camera' => ['numeric'],
                'back_camera' => ['numeric'],

                'graphics' => ['numeric'],
                'cpu' => ['string', 'min:3'],

                'size' => ['string', 'min:3'],
                'gender' => ['string', 'min:3'],

                'quantity' => ['numeric'],
                'price' => ['numeric', 'max:9000000000'],
                'date_of_production' => ['date'],
                'new_or_old' => ['string', 'max:3'],
                'discount' => ['numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product->update($request->all());
            if ($category) {
                $category->update($request->all());
            }
            if ($computer) {
                $computer->update($request->all());
            } elseif ($cloth) {
                $cloth->update($request->all());
            } elseif ($mobile) {
                $mobile->update($request->all());
            } elseif ($electrical) {
                $electrical->update($request->all());
            }


//            return response()->json(['message' => 'The Product was Updated Successfully.']);
            return response()->json($product, Response::HTTP_OK);
        }
        return response()->json(['message' => 'Update is not Allowed.']);
    }


    public function destroy($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        if (Auth::id() == $product->account_id) {
            $product->comments()->delete();
            $product->likes()->delete();
            $product->category()->delete();
            $product->views()->delete();
            $product->rates()->delete();
            $product->images()->delete();
            $product->carts()->delete();
            $product->delete();

            return response()->json(['message' => 'The Product was Deleted Successfully.']);
        }

        return response()->json(['message' => 'Delete is not Allowed.']);
    }


    public function buy($product_id)
    {

        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        if ($product->quantity <= 0) {
            return response()->json(['message' => 'The product was removed from the store because it is the last piece.']);
        }
        $walletUser = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();


        if (($walletUser->value) >= (($product->price) + ((($product->price) * (2)) / 100))) {

            $product->update([
                'quantity' => $product->quantity - 1
            ]);

            $shipping = Shipping::query()->where('account_id', Auth::id())->first();

            if (!$shipping) {
                $shipping = Shipping::query()->create([
                    'account_id' => Auth::id()
                ]);
            }

            $newCost = (($shipping->cost) + ((($product->price) * (4)) / 100));

            $shipping->update([
                'cost' => $newCost
            ]);

            $walletUser->update([
                'value' => ($walletUser->value) - ($product->price)
            ]);

            $wallet = Wallet::query()->find(1);
            $wallet->update([
                'value' => ($wallet->value) + ($product->price)
            ]);

            Notification::query()
                ->create([
                    'account_id' => $product->account_id,
                    'content' => 'تم شراء منتجك'
                ]);

            $product->update([
                'buy_at' => now()
            ]);

            $shipping->products()->attach($product);

            if ($product->quantity <= 0) {
//                $product->comments()->delete();
//                $product->likes()->delete();
//                $product->category()->delete();
//                $product->views()->delete();
//                $product->rates()->delete();
//                $product->images()->delete();
//                $product->carts()->delete();
//                $product->delete();
                return response()->json(['message' => 'The product was bought and removed from the store because it is the last piece.']);
            }
            $view = View::query()
                ->where('account_id', Auth::id())
                ->where('product_id', $product_id)
                ->first();
            if (!$view) {
                View::query()->create([
                    'account_id' => Auth::id(),
                    'product_id' => $product_id
                ]);
                $product->increment('viewer');
            }
            $product->increment('number_of_sales');

            return response()->json(['message' => 'The product was bought.']);
        }
        return response()->json(['message' => 'Your money is not enough.']);
    }


    public function search(Request $request)
    {
        $category_type = $request->query('type');
        $name_of_owner = $request->query('owner');
        $name_of_product = $request->query('product');
        $Query = null;

        if (!$category_type && !$name_of_owner && !$name_of_product) {
            return response()->json(['message' => 'You Must Enter The Name Of The Product  or its Category Type or its Name Of Owner']);
        } else {

            if ($category_type) {
                if ($category_type == 'electrical') {
                    $Query = Category::query()->with('products', 'electricals')->get();

                } else if ($category_type == 'mobile') {
                    $Query = Category::query()->with('products', 'mobiles')->get();

                } else if ($category_type == 'computer') {
                    $Query = Category::query()->with('products', 'computers')->get();

                } else if ($category_type == 'cloth') {
                    $Query = Category::query()->with('products', 'cloths')->get();

                }
                if (!$Query) {
                    return response()->json(['message' => 'There is Error in your input.']);
                }
                return response()->json($Query, Response::HTTP_OK);
            }

            if ($name_of_owner) {
                $Query = Account::query()
                    ->where('FullName', $name_of_owner)
                    ->with('products')
                    ->get();

                return response()->json($Query, Response::HTTP_OK);
            }

            if ($name_of_product) {
                $Query = Product::query()
                    ->where('name_of_product', $name_of_product)
                    ->with('category', 'account')
                    ->get();
                return response()->json($Query, Response::HTTP_OK);
            }
        }
    }


    public function order($request, Request $request1)
    {
        $productQuery = Product::query();
        if ($request == 'DESC') {
            $productQuery->orderBy('price', 'DESC');
        } elseif ($request == 'ASC') {
            $productQuery->orderBy('price', 'ASC');
        } elseif ($request == 'like') {
            $productQuery->orderBy('likes_count', 'DESC');
        } elseif ($request == 'view') {
            $productQuery->orderBy('viewer', 'DESC');
        } elseif ($request == 'sales') {
            $productQuery->orderBy('number_of_sales', 'DESC');
        } elseif ($request == 'price') {
            $price_from = $request1->query('price_from');
            $price_to = $request1->query('price_to');
            if ($price_from) {
                $productQuery->where('price', '>=', $price_from);
            }
            if ($price_to) {
                $productQuery->where('price', '<', $price_to);
            }
        }
        $products = $productQuery->get();
        return response()->json($products);
    }

    public function UsedProducts()
    {
        $product = Product::query()
            ->where('new_or_old', '=', 'old')
            ->get();
        if (!$product) {
            return response()->json(['message' => 'There are no used products.']);
        }
        return response()->json($product);
    }


    public function buyCart($cart_id)
    {
        $cart = Cart::query()
            ->find($cart_id);

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        $wallet = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();

        $price = $cart->products->pluck('price');

        $pricess = 0;

        foreach ($price as $prices) {
            $pricess = $pricess + $prices + (($prices * 2) / 100);
        }

        Notification::query()
            ->create([
                'account_id' => $cart->account_id,
                'content' => 'تم خصم مبلغ من محفظتك'
            ]);

        if ($pricess >= 5000000) {
            $string = str::random(10);

            Coupon::query()
                ->create([
                    'code' => $string,
                    'percent' => 0.10,
                    'account_id' => Auth::id()
                ]);
        }

        if ($pricess <= $wallet->value) {
            while (true) {
                $product = DB::table('cart_product')
                    ->where('cart_id', '=', $cart_id)
                    ->first();
                if (!$product) {
                    return response()->json($cart, Response::HTTP_OK);
                }
                $pro = $product->product_id;

                $this->buy($pro);

                DB::table('cart_product')
                    ->where('id', '=', $product->id)
                    ->delete();
            }
        }
        return response()->json(['message' => 'Your money is not enough.']);
    }

    public function useCoupon($cart_id,Request $request){
        $cart = Cart::query()
            ->find($cart_id);

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        $wallet = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();

        $price = $cart->products->pluck('price');

        $pricess = 0;

        foreach ($price as $prices) {
            $pricess = $pricess + $prices + (($prices * 2) / 100);
        }

        $code = $request->code;

        $coupon = Coupon::query()
            ->where('code','=',$code)
            ->where('account_id','=',Auth::id())
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'There are no coupons.']);
        }

        $newPrice = $pricess*($coupon->percent);

        $wallet->update([
            'value' => ($wallet->value) + ($newPrice)
        ]);

        $adminWallet = Wallet::query()
            ->find(1);

        $adminWallet->update([
            'value' => ($adminWallet->value) - ($newPrice)
        ]);

        $this->buyCart($cart_id);

        return response()->json(['message' => 'Coupon used successfully.']);

    }

    public function useCouponGift($cart_id,Request $request){
        $cart = Cart::query()
            ->find($cart_id);

        if (!$cart) {
            return response()->json(['message' => 'The cart is not found.']);
        }

        $wallet = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();

        $price = $cart->products->pluck('price');

        $pricess = 0;

        foreach ($price as $prices) {
            $pricess = $pricess + $prices + (($prices * 2) / 100);
        }

        $code = $request->code;

        $coupon = CouponGift::query()
            ->where('code','=',$code)
            ->where('account_id','=',Auth::id())
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'There are no coupon gifts.']);
        }

        $newPrice = $coupon->money;

        $wallet->update([
            'value' => ($wallet->value) + ($newPrice)
        ]);

        $this->buyCart($cart_id);

        return response()->json(['message' => 'Coupon gift used successfully.']);

    }


    public function book($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        $product->update([
            'booked' => true,
        ]);

        $walletUser = Wallet::query()
            ->where('account_id', '=', Auth::id())
            ->first();

        $walletUser->update([
            'value' => ($walletUser->value) - (($product->price) * 0.015)
        ]);

        $wallet = Wallet::query()->find(1);
        $wallet->update([
            'value' => ($wallet->value) + (($product->price) * 0.015)
        ]);

        return response()->json($product, Response::HTTP_OK);

    }

    public function showBook()
    {
        $product = Product::query()
            ->where('booked', '=', true)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'There are no Products.']);
        }

        $currentTime = Carbon::parse($product->booked_at);
        $currentTime->addDays(3);

        if (now() <= $currentTime) {
            return response()->json($product, Response::HTTP_OK);
        }

        $product->update([
            'booked' => false,
            'booked_at' => null
        ]);

        return response()->json(['message' => 'Booking period has expired.']);

    }

    public function showbuy()
    {
        $product = Product::query()
            ->where('buy_at', '!=', null)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'There are no Products.']);
        }

        $currentTime = Carbon::parse($product->buy_at);
        $currentTime->addDays(3);

        if (now() <= $currentTime) {
            return response()->json($product, Response::HTTP_OK);
        }

        return response()->json(['message' => 'product return period has expired.']);

    }

    public function returnProduct($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'The Product is not Found.']);
        }

        $currentTime = Carbon::parse($product->buy_at);
        $currentTime->addDays(3);

        if (now() <= $currentTime) {
            $product->update([
                'buy_at' => null
            ]);

            $walletUser = Wallet::query()
                ->where('account_id', '=', Auth::id())
                ->first();

            $walletComp = Wallet::query()
                ->where('account_id', '=', $product->account_id)
                ->first();

            $product->update([
                'quantity' => $product->quantity + 1,
                'number_of_sales' => $product->number_of_sales + 1
            ]);

            $shipping = Shipping::query()->where('account_id', Auth::id())->first();


            $newCost = (($shipping->cost) - ((($product->price) * (4)) / 100));

            $shipping->update([
                'cost' => $newCost
            ]);

            $walletUser->update([
                'value' => ($walletUser->value) + (($product->price)*0.98)
            ]);

            $walletComp->update([
                'value' => ($walletComp->value) - (($product->price)*0.92)
            ]);

            $wallet = Wallet::query()->find(1);
            $wallet->update([
                'value' => ($wallet->value) - (($product->price)*0.98)
            ]);

            Notification::query()
                ->create([
                    'account_id' => $product->account_id,
                    'content' => 'تم ارجاع منتجك'
                ]);

            $view = View::query()
                ->where('account_id', Auth::id())
                ->where('product_id', $product_id)
                ->first();
            if (!$view) {
                View::query()->create([
                    'account_id' => Auth::id(),
                    'product_id' => $product_id
                ]);
                $product->increment('viewer');
            }
        }
        return response()->json(['message' => 'product return period has expired.']);
    }



}
