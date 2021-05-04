<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\UserProductGroup;
use \Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * Api endpoint for adding product in cart
     */
    public function addProductInCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id'
        ]);

        $cartData = $request->all();
        $cartData['user_id'] = 1;
        $cart = new Cart();
        $cart->setFields($cartData);
        if (!$cart->save()) {
            return response()->json([
                'success' => false
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * Api endpoint for removing product from cart
     */
    public function removeProductFromCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id'
        ]);

        $deletedItems = Cart::where(['id' => $request->post('product_id')])
            ->where(['user_id' => 1])
            ->delete();
        if ($deletedItems == 0) {
            return response()->json([
                'success' => false
            ]);
        }
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * Api endpoint for setting cart product quantity
     */
    public function setCartProductQuantity(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer'
        ]);

        Cart::where(['product_id' => $request->post('product_id')])
            ->where(['user_id' => 1])
            ->update(['quantity' => $request->post('quantity')]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Api endpoint for getting user cart data
     */
    public function getUserCart(Request $request)
    {
        $groupsWithItems = UserProductGroup::with('productGroupItems')
            ->where(['user_id' => 1])
            ->get();

        $cartItems = Cart::select('cart.product_id', 'cart.quantity', 'products.price')
            ->leftJoin('products', 'products.id', '=', 'cart.product_id')
            ->where(['cart.user_id' => 1])
            ->get()
            ->toArray();
        $productsIds = array_column($cartItems, 'product_id');

        $cartItemsMap = [];
        foreach ($cartItems as $cartItem) {
            $cartItemsMap[$cartItem['product_id']] = $cartItem;
        }


        $groupItemIds = [];
        $groupItems = [];
        foreach ($groupsWithItems as $groupsWithItem) {
            foreach ($groupsWithItem->productGroupItems as $groupItem) {
                $groupItemIds[$groupsWithItem->id][] = $groupItem->product_id;
                $groupItems[$groupsWithItem->id][] = [
                    'discount' => $groupsWithItem->discount,
                    'item' => $groupItem,
                ];
            }
        }

        $discountedGroupsIds = [];
        foreach ($groupItemIds as $groupId => $productIds) {
            if (count(array_intersect_key(array_flip($productsIds), array_flip($productIds))) === count($productIds)) {
                $discountedGroupsIds[] = $groupId;
            }
        }

        $discountedGroupItems = [];
        foreach ($discountedGroupsIds as $discountedGroupId) {
            foreach ($groupItems[$discountedGroupId] as $groupItem) {
                $discountedGroupItems[$discountedGroupId][] = [
                    'discount' => $groupItem['discount'],
                    'quantity' => $cartItemsMap[$groupItem['item']['product_id']]['quantity'],
                    'price' => $cartItemsMap[$groupItem['item']['product_id']]['price']
                ];
            }
        }

        $totalDiscount = 0;
        foreach ($discountedGroupItems as $discountedGroupItem) {
            $minQuantity = min(array_column($discountedGroupItem, 'quantity'));
            $discount = $discountedGroupItem[0]['discount'] / 100;
            $prices = array_column($discountedGroupItem, 'price');
            foreach ($prices as $price) {
                $totalDiscount += $price * $minQuantity * $discount;
            }
        }


        return response()->json([
            'discount' => $totalDiscount,
            'products' => $cartItems
        ]);
    }
}
