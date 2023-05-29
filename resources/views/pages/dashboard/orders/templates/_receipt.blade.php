<?php
use App\Models\Order;
use App\Classes\Hook;
use Illuminate\Support\Facades\View;

?>
<div class="w-full h-full">
    <div class="w-full md:w-1/2 lg:w-1/3 shadow-lg bg-white p-1 mx-auto">
        <div class="flex items-center justify-center">
            @if ( empty( ns()->option->get( 'ns_invoice_receipt_logo' ) ) )
            <h3 class="text-3xl font-bold">{{ ns()->option->get( 'ns_store_name' ) }}</h3>
            @else
            <img src="{{ ns()->option->get( 'ns_invoice_receipt_logo' ) }}" alt="{{ ns()->option->get( 'ns_store_name' ) }}">
            @endif
        </div>
        <div class="p-1  border-black text-xxs">
            <div class="flex flex-wrap -mx-2  items-center justify-center">
                183 Nguyễn Thị Minh Khai, Nha Trang, Khánh Hòa
            </div>
            <div class="flex flex-wrap -mx-2 items-center justify-center">
                0935 763 999
            </div>
        </div>

        <div class="p-1  border-black">
            <div class="flex flex-wrap -mx-2 items-center justify-center text-lg font-bold">
                HÓA ĐƠN THANH TOÁN
            </div>
            <div class="flex flex-wrap -mx-2 text-xxs items-center justify-center">
                <span class="font-bold">Số:</span> &nbsp;{{ $order->code }}
            </div>
        </div>
        
        <div class="p-1 border-b border-black ">
            <div class="flex flex-wrap -mx-2 text-xxs ">
                <div class="px-2 font-bold" style="width: 5rem!important">
                    Ngày:
                </div>
                <div class="px-2">
                    {{ ns()->date->getFormatted( $order->created_at )}}
                </div>
            </div>

            <div class="flex flex-wrap -mx-2 text-xxs ">
                <div class="px-2 font-bold" style="width: 5rem!important">
                    Thu ngân:
                </div>
                <div class="px-2">
                    {{ $order->user->username }}
                </div>
            </div>

            <div class="flex flex-wrap -mx-2 text-xxs ">
                <div class="px-2 font-bold" style="width: 5rem!important">
                    Khách hàng:
                </div>
                <div class="px-2">
                    {{ $order->customer->name }}
                </div>
            </div>
            <div class="flex flex-wrap -mx-2 text-xxs ">
                <div class="px-2 font-bold" style="width: 5rem!important">
                    Địa chỉ:
                </div>
                <div class="px-2" style="width: 70%!important">
                    {{ $order->billing_address->address_1 }} - {{ $order->billing_address->phone }}
                </div>
            </div>
        </div>
        
        <!-- <div class="p-1 border-b border-black">
            <div class="flex flex-wrap -mx-2 text-xxs">
                <div class="px-2 w-1/2">
                    {!! nl2br( $ordersService->orderTemplateMapping( 'ns_invoice_receipt_column_a', $order ) ) !!}
                </div>
                <div class="px-2 w-1/2">
                    {!! nl2br( $ordersService->orderTemplateMapping( 'ns_invoice_receipt_column_b', $order ) ) !!}
                </div>
            </div>
        </div> -->
        <div class="table w-full">
            <table class="w-full">
                <tbody>
                    <tr class="text-xxs font-semibold">
                        <td class="w-1/2 p-1 border-b border-l  border-black">{{ __( 'Product' ) }}</td>
                        <td class="p-1 border-b border-r border-black text-right">{{ __( 'Sub Total' ) }}</td>
                    </tr>
                </tbody>
                <tbody class="text-xxs">
                    @foreach( Hook::filter( 'ns-receipt-products', $order->combinedProducts ) as $product )
                    <tr class="border-l border-r border-black">
                        <td class="p-1 border-dashed border-b border-black" style="width: 70%!important">
                            <span class="">{{ $product->name }}</span>
                            <br>
                            <span class="text-xxs">({{ $product->quantity }} {{ $product->unit->name }} x {{ ns()->currency->define( $product->unit_price ) }})</span>
                        </td>
                        <td class="p-1 border-dashed border-b border-black text-right">{{ ns()->currency->define( $product->total_price ) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tbody>
                    <tr class="border-t border-black">
                        <td class="w-1/2 p-1 text-xxs font-semibold">{{ __( 'Sub Total' ) }}</td>
                        <td class="p-1 text-xxs text-right">{{ ns()->currency->define( $order->subtotal ) }}</td>
                    </tr>
                    @if ( $order->discount > 0 )
                    <tr>
                        <td class="w-1/2 p-1 border-black text-xxs font-semibold">
                            <span>{{ __( 'Discount' ) }}</span>
                            @if ( $order->discount_type === 'percentage' )
                            <span>({{ $order->discount_percentage }}%)</span>
                            @endif
                        </td>
                        <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $order->discount ) }}</td>
                    </tr>
                    @endif
                    @if ( $order->total_coupons > 0 )
                    <tr>
                        
                        <td class="w-1/2 p-1 border-black text-xxs font-semibold">
                            <span>{{ __( 'Coupons' ) }}</span>
                        </td>
                        <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $order->total_coupons ) }}</td>
                    </tr>
                    @endif
                    @if ( ns()->option->get( 'ns_invoice_display_tax_breakdown' ) === 'yes' ) 
                        @foreach( $order->taxes as $tax )
                        <tr>
                            <td class="w-1/2 p-1 border-black text-xxs font-semibold">
                                <span>{{ $tax->tax_name }} &mdash; {{ $order->tax_type === 'inclusive' ? __( 'Inclusive' ) : __( 'Exclusive' ) }}</span>
                            </td>
                            <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $tax->tax_value ) }}</td>
                        </tr>
                        @endforeach
                    @else                     
                        @if ( $order->tax_value > 0 )
                        <tr>
                            <td class="w-1/2 p-1 border-black text-xxs font-semibold">
                                <span>{{ __( 'Taxes' ) }}</span>
                            </td>
                            <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $order->tax_value ) }}</td>
                        </tr>
                        @endif
                    @endif
                    @if ( $order->shipping > 0 )
                    <tr>
                        <td class="w-1/2 p-1 border-black text-xxs font-semibold">{{ __( 'Shipping' ) }}</td>
                        <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $order->shipping ) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="w-1/2 p-1 border-b border-black text-xxs font-semibold">{{ __( 'Total' ) }}</td>
                        <td class="p-1 border-b border-black text-xxs text-right">{{ ns()->currency->define( $order->total ) }}</td>
                    </tr>
                    @foreach( $order->payments as $payment )
                    <tr>
                        <td class="p-1 text-xxs font-semibold">{{ $paymentTypes[ $payment[ 'identifier' ] ] ?? __( 'Unknown Payment' ) }}</td>
                        <td class="p-1 text-xxs text-right">{{ ns()->currency->define( $payment[ 'value' ] ) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="w-1/2 p-1 border-black text-xxs font-semibold">{{ __( 'Paid' ) }}</td>
                        <td class="p-1 border-black text-xxs text-right">{{ ns()->currency->define( $order->tendered ) }}</td>
                    </tr>
                    @if ( in_array( $order->payment_status, [ 'refunded', 'partially_refunded' ]) )
                        @foreach( $order->refund as $refund )
                        <tr>
                            <td class="w-1/2 p-1 border-b border-black text-xxs font-semibold">{{ __( 'Refunded' ) }}</td>
                            <td class="p-1 border-b border-black text-xxs text-right">{{ ns()->currency->define( - $refund->total ) }}</td>
                        </tr>
                        @endforeach
                    @endif
                    @switch( $order->payment_status )
                        @case( Order::PAYMENT_PAID )
                        <tr>
                            <td class="w-1/2 p-1 border-b border-black text-xxs font-semibold">{{ __( 'Change' ) }}</td>
                            <td class="p-1 border-b border-black text-xxs text-right">{{ ns()->currency->define( $order->change ) }}</td>
                        </tr>
                        @break
                        @case( Order::PAYMENT_PARTIALLY )
                        <tr>
                            <td class="w-1/2 p-1 border-b border-black text-xxs font-semibold">{{ __( 'Due' ) }}</td>
                            <td class="p-1 border-b border-black text-xxs text-right">{{ ns()->currency->define( abs( $order->change ) ) }}</td>
                        </tr>
                        @break
                    @endswitch
                </tbody>
            </table>
            @if( $order->note_visibility === 'visible' )
            <div class="pt-6 pb-4 text-center text-xxs">
                <strong>{{ __( 'Note: ' ) }}</strong> {{ $order->note }}
            </div>
            @endif
            <div class="pt-6 pb-4 text-center text-xxs">
                {{ ns()->option->get( 'ns_invoice_receipt_footer' ) }}
            </div>
        </div>
    </div>
</div>
@includeWhen( request()->query( 'autoprint' ) === 'true', '/pages/dashboard/orders/templates/_autoprint' )