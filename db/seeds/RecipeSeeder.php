<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RecipeSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'featured' => 1,
                'description' => '',
                'name' => 'Deviled Eggs',
                'ingredients' => '6 hard boiled eggs\n1 tsp. Worcestershire sauce\n1/2 tsp. salt\n3/4 tsp. mustard\n2 T. lemon juice or pickle brine\n1/8 tsp. ground pepper\n3 T. mayonnaise or salad dressing',
                'instructions' => 'Cut eggs in half. Remove yolks, smash yokes with fork. Combine yolks with remaining ingredients. Mix until smooth. Add mixture to egg whites. Using a spoon or a pastry bag.\nNote. I fill a gallon Ziploc bag with the yolk mixture and cut off the corner and squeeze it onto the white.',
                'category' => 'Appetizers',
                'created_at' => date('2025-03-23 18:03:29'),
            ],
            [
                'featured' => 0,
                'description' => '',
                'name' => 'Easy Christmas Wassail',
                'ingredients' => '4 c. water\n4 cinnamon sticks\n8 allspice berries\n10 whole cloves\n1/2 c. white sugar\n3 c. apple cider\n1 1/2 c. orange juice',
                'instructions' => 'Combine all ingredients in a large pot and then bring to a boil. Simmer wassail until you are ready to serve. Garnish with fresh cinnamon sticks or oranges. Makes 12 cups',
                'category' => 'Beverages',
                'created_at' => date('2025-03-23 17:03:39'),
            ],
        ];

        foreach ($data as $recipe) {
            // Escape the recipe name for safe usage in the query
            $escapedName = addslashes($recipe['name']);
            $sql = "SELECT COUNT(*) AS count FROM recipes WHERE name = '$escapedName'";
            $exists = $this->fetchRow($sql);

            if ($exists['count'] == 0) {
                $this->table('recipes')->insert($recipe)->save();
            }
        }
    }
}
