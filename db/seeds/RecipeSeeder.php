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
            [
                'featured' => 1,
                'description' => '',
                'name' => 'Quick Cranberry Punch',
                'ingredients' => '2 cans (6-ounces each) frizen lemonade concentrate, thawed\n 1 1/3 C cold water\n 2 bottles (32-ounces each) cranberry juice cocktail, chilled\n 4 cans (12-ounces each) ginger-ale, chilled\n Ice ring or Ice',
                'instructions' => 'Mix lemonade concentrate and water in large pitcher. Stir in cranberry juice cocktail. Just before serving, stir in ginger-ale. Pour into large punch bowl. Add ice Ring. Yield: 24 servings\n Note: Festive ice ring: fill a ring mold or bundt cake pan with crushed ice (smaller than your punch bowl). Cut fruit (lemons, limes, oranges, and starfruit) and 1/4-inch slices; arrange in the ice so the fruit sticks up above the top of the ring mold. Freeze overnight until solid. When ready to serve, run hot water over bottom or the mold to loosen the ice. Float in punch',
                'category' => 'Beverages',
                'created_at' => date('2025-03-25 19:05:00'),
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
