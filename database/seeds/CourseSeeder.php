<?php

use App\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::create(['title' => 'ACADEMIA DE IMPORTAÇÃO - MISTER INS','hotmart_id'=> 200860]);
        Course::create(['title' => 'ACADEMIA DE IMPORTAÇÃO 2.0','hotmart_id'=> 991555]);
        Course::create(['title' => 'ACADEMIA DE IMPORTAÇÃO UPSELL','hotmart_id'=> 1425045]);
        Course::create(['title' => 'ACADEMIA DO INSTAGRAM - MISTER INS','hotmart_id'=> 900033]);
        Course::create(['title' => 'MENTORIA MR INS LVL 01','hotmart_id'=> 1454255]);
        Course::create(['title' => 'MISTER MIND 1.0','hotmart_id'=> 1406204]);
        Course::create(['title' => 'O Poder da Autoridade | por Bruno Pereira','hotmart_id'=> 1337447]);
        Course::create(['title' => 'PARCEIRO DE NEGÓCIOS MISTER INS 1.0','hotmart_id'=> 1442311]);
        Course::create(['title' => 'PROJ X - PARCEIRO DE NEGÓCIOS MISTER INS','hotmart_id'=> 448026]);
        Course::create(['title' => 'UPGRADE ADI','hotmart_id'=> 1042759]);
        Course::create(['title' => 'VIVENDO DE IPHONE','hotmart_id'=> 1180191]);
    }
}
