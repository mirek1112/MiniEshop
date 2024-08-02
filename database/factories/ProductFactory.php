<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomImages =[
            'https://image.alza.cz/products/NL258d1a1/NL258d1a1.jpg',
            'https://image.alza.cz/products/RI045b1/RI045b1.jpg',
            'https://image.alza.cz/products/NOTHwP2o/NOTHwP2o.jpg',
            'https://image.alza.cz/products/XBSE0540/XBSE0540.jpg',
            'https://image.alza.cz/products/MSX0052/MSX0052.jpg',
            'https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco/wbatnncdieqtbsfar8mr/NIKE+AIR+MAX+PLUS+%28PS%29.png',
            'https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco/5a7cd144-04d2-47ba-832e-10faa2bf7c75/NIKE+DUNK+LOW+NN.png',
            'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/0530fc36-3291-4f48-b558-e0fd31545d06/trail-repel-mid-rise-8cm-brief-lined-running-shorts-1r97Wl.png',
            'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/54d9612f-6c92-4089-bcc3-229cb0406bdb/alphafly-3-road-racing-shoes-F52QTm.png',
            'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/2372799e-7c0c-4ae0-87e0-5c07f932cb27/dri-fit-club-unstructured-featherlight-cap-VBkWKP.png'
       ];
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 1, 9999),
            'quantity' => $this->faker->numberBetween(1, 100),
            'image' => $randomImages[rand(0, 9)],
            'category_id' => Category::inRandomOrder()->first()->id,
        ];

    }
}
