<?php
use App\Models\Order;
use App\Classes\Hook;
use Illuminate\Support\Facades\View;

?>
<div class="w-full h-full">
    <div class="w-full md:w-1/2 lg:w-1/3 shadow-lg bg-white p-2 mx-auto">
        <div class="flex items-center justify-center">
            @if ( empty( ns()->option->get( 'ns_invoice_receipt_logo' ) ) )
            <h3 class="text-3xl font-bold">{{ ns()->option->get( 'ns_store_name' ) }}</h3>
            @else
            <img src="{{ ns()->option->get( 'ns_invoice_receipt_logo' ) }}" alt="{{ ns()->option->get( 'ns_store_name' ) }}">
            @endif
        </div>
        <div class="p-2  border-gray-700">
            <div class="flex flex-wrap -mx-2 text-sm items-center justify-center">
                183 Nguyễn Thị Minh Khai - Nha Trang - Khánh Hòa
            </div>
            <div class="flex flex-wrap -mx-2 text-sm items-center justify-center">
                0935 763 999
            </div>
        </div>


        <div class="p-2  border-gray-700">
            <div class="flex flex-wrap -mx-2 text-sm items-center justify-center text-2xl font-bold">
                HÓA ĐƠN THANH TOÁN
            </div>
            <div class="flex flex-wrap -mx-2 text-sm items-center justify-center">
                Số : {{ $order->code }}
            </div>
        </div>
        
        <div class="p-2 border-b border-gray-700 ">
            <div class="flex flex-wrap -mx-2 text-sm ">
                <div class="px-2 w-36 font-bold">
                    Ngày:
                </div>
                <div class="px-2">
                    {{ ns()->date->getFormatted( $order->created_at )}}
                </div>
            </div>

            <div class="flex flex-wrap -mx-2 text-sm ">
                <div class="px-2 w-36 font-bold">
                    Thu ngân:
                </div>
                <div class="px-2">
                    {{ $order->user->username }}
                </div>
            </div>

            <div class="flex flex-wrap -mx-2 text-sm ">
                <div class="px-2 w-36 font-bold">
                    Khách hàng:
                </div>
                <div class="px-2">
                    {{ $order->customer->name }}
                </div>
            </div>
            <div class="flex flex-wrap -mx-2 text-sm ">
                <div class="px-2 w-36 font-bold">
                    Địa chỉ:
                </div>
                <div class="px-2">
                    {{ $order->billing_address->address_1 }} ({{ $order->billing_address->phone }})
                </div>
            </div>
        </div>
        
        <!-- <div class="p-2 border-b border-gray-700">
            <div class="flex flex-wrap -mx-2 text-sm">
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
                <thead>
                    <tr class="font-semibold">
                        <td class="p-2 border-b border-l  border-gray-800">{{ __( 'Product' ) }}</td>
                        <td class="p-2 border-b border-gray-800">SL</td>
                        <td class="p-2 border-b border-gray-800">{{ __( 'Unit Price' ) }}</td>
                        <td class="p-2 border-b border-r border-gray-800 text-right">{{ __( 'Sub Total' ) }}</td>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach( Hook::filter( 'ns-receipt-products', $order->combinedProducts ) as $product )
                    <tr class="border-l border-r border-gray-700">
                        <td class="p-2 border-dashed border-b border-gray-700">
                            <span class="">{{ $product->name }}</span>
                            <br>
                            <span class="text-xs text-gray-600"></span>
                        </td>
                        <td class="p-2 border-dashed border-b border-gray-700">
                        {{ $product->quantity }} {{ $product->unit->name }}
                        </td>
                        <td class="p-2 border-dashed border-b border-gray-700">
                        {{ ns()->currency->define( $product->unit_price ) }}
                        </td>
                        <td class="p-2 border-dashed border-b border-gray-800 text-right">{{ ns()->currency->define( $product->total_price ) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tbody>
                    <tr class="border-t border-gray-700">
                        <td colspan="2"></td>
                        <td class="p-2 text-sm font-semibold">{{ __( 'Sub Total' ) }}</td>
                        <td class="p-2 text-sm text-right">{{ ns()->currency->define( $order->subtotal ) }}</td>
                    </tr>
                    @if ( $order->discount > 0 )
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 border-gray-800 text-sm font-semibold">
                            <span>{{ __( 'Discount' ) }}</span>
                            @if ( $order->discount_type === 'percentage' )
                            <span>({{ $order->discount_percentage }}%)</span>
                            @endif
                        </td>
                        <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->discount ) }}</td>
                    </tr>
                    @endif
                    @if ( $order->total_coupons > 0 )
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 border-gray-800 text-sm font-semibold">
                            <span>{{ __( 'Coupons' ) }}</span>
                        </td>
                        <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->total_coupons ) }}</td>
                    </tr>
                    @endif
                    @if ( ns()->option->get( 'ns_invoice_display_tax_breakdown' ) === 'yes' ) 
                        @foreach( $order->taxes as $tax )
                        <tr>
                            <td colspan="2"></td>
                            <td class="p-2 border-gray-800 text-sm font-semibold">
                                <span>{{ $tax->tax_name }} &mdash; {{ $order->tax_type === 'inclusive' ? __( 'Inclusive' ) : __( 'Exclusive' ) }}</span>
                            </td>
                            <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $tax->tax_value ) }}</td>
                        </tr>
                        @endforeach
                    @else                     
                        @if ( $order->tax_value > 0 )
                        <tr>
                            <td colspan="2"></td>
                            <td class="p-2 border-gray-800 text-sm font-semibold">
                                <span>{{ __( 'Taxes' ) }}</span>
                            </td>
                            <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->tax_value ) }}</td>
                        </tr>
                        @endif
                    @endif
                    @if ( $order->shipping > 0 )
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 border-gray-800 text-sm font-semibold">{{ __( 'Shipping' ) }}</td>
                        <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->shipping ) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 border-b border-gray-800 text-sm font-semibold">{{ __( 'Total' ) }}</td>
                        <td class="p-2 border-b border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->total ) }}</td>
                    </tr>
                    @foreach( $order->payments as $payment )
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 text-sm font-semibold">{{ $paymentTypes[ $payment[ 'identifier' ] ] ?? __( 'Unknown Payment' ) }}</td>
                        <td class="p-2 text-sm text-right">{{ ns()->currency->define( $payment[ 'value' ] ) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td class="p-2 border-gray-800 text-sm font-semibold">{{ __( 'Paid' ) }}</td>
                        <td class="p-2 border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->tendered ) }}</td>
                    </tr>
                    @if ( in_array( $order->payment_status, [ 'refunded', 'partially_refunded' ]) )
                        @foreach( $order->refund as $refund )
                        <tr>
                            <td colspan="2"></td>
                            <td class="p-2 border-b border-gray-800 text-sm font-semibold">{{ __( 'Refunded' ) }}</td>
                            <td class="p-2 border-b border-gray-800 text-sm text-right">{{ ns()->currency->define( - $refund->total ) }}</td>
                        </tr>
                        @endforeach
                    @endif
                    @switch( $order->payment_status )
                        @case( Order::PAYMENT_PAID )
                        <tr>
                            <td colspan="2"></td>
                            <td class="p-2 border-b border-gray-800 text-sm font-semibold">{{ __( 'Change' ) }}</td>
                            <td class="p-2 border-b border-gray-800 text-sm text-right">{{ ns()->currency->define( $order->change ) }}</td>
                        </tr>
                        @break
                        @case( Order::PAYMENT_PARTIALLY )
                        <tr>
                            <td colspan="2"></td>
                            <td class="p-2 border-b border-gray-800 text-sm font-semibold">{{ __( 'Due' ) }}</td>
                            <td class="p-2 border-b border-gray-800 text-sm text-right">{{ ns()->currency->define( abs( $order->change ) ) }}</td>
                        </tr>
                        @break
                    @endswitch
                </tbody>
            </table>
            @if( $order->note_visibility === 'visible' )
            <div class="pt-6 pb-4 text-center text-gray-800 text-sm">
                <strong>{{ __( 'Note: ' ) }}</strong> {{ $order->note }}
            </div>
            @endif
            <div class="pt-6 pb-4 text-center text-gray-800 text-sm">
                {{ ns()->option->get( 'ns_invoice_receipt_footer' ) }}
            </div>
        </div>
    </div>
</div>
@includeWhen( request()->query( 'autoprint' ) === 'true', '/pages/dashboard/orders/templates/_autoprint' )