import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/donate_campaigns_page.dart';
import 'package:charity_project/view/donation_categories_page.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
final List<Map<String, dynamic>> Donaition = [
  {
    'category': 'Campaigns',
    'tabs': [
      {'id': 'all', 'name': 'All'},
      {'id': 'water', 'name': 'Water'},
      {'id': 'food', 'name': 'Food'},
      {'id': 'construction', 'name': 'Construction'},
      {'id': 'health', 'name': 'Health'},
      {'id': 'education', 'name': 'Education'},
      {'id': 'orphan_support', 'name': 'Orphan Support'},
    ],
  },
  {
    'category': 'Sponsorships',
    'tabs': [
      {'id': 'all', 'name': 'All'},
      {'id': 'orphan', 'name': 'Orphan'},
      {'id': 'student', 'name': 'Student'},
      {'id': 'poor_families', 'name': 'Poor Families'},
      {'id': 'special_needs', 'name': 'People with Special Needs'},
    ],
  },
  {
    'category': 'HumanitarianCases',
    'tabs': [
      {'id': 'all', 'name': 'All'},
      {'id': 'debtors', 'name': 'Debtors'},
      {'id': 'patients', 'name': 'Patients'},
      {'id': 'needy_families', 'name': 'Needy Families'},
      {'id': 'student', 'name': 'Student'},
    ],
  },
  {
    'category': 'CharityTypes',
    'tabs': [
      {'id': 'feeding', 'name': 'Feeding a Poor'},
      {'id': 'clothing', 'name': 'Clothing a Poor'},
      {'id': 'oath_expiation', 'name': 'Oath Expiation'},
      {'id': 'vow', 'name': 'Vow'},
      {'id': 'slaughter', 'name': 'Slaughter'},
      {'id': 'aqiqah', 'name': 'Aqiqah'},
    ],
  },
];
 
class CampaignsPage extends StatefulWidget {
  final String category;

  const CampaignsPage({super.key,required this.category});

  @override
  State<CampaignsPage> createState() => _CampaignsPageState();
}

class _CampaignsPageState extends State<CampaignsPage> {
  late List<Map<String, String>> campaignCategories;

  @override
  void initState() {
    super.initState();
    
     campaignCategories = Donaition
        .firstWhere((element) => element['category'] == widget.category)['tabs']
        .cast<Map<String, String>>();
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: campaignCategories.length,
      child: Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(
          backgroundColor: AppColors.white,
          title: Text(widget.category, style: AppTextStyle.a),
          bottom: TabBar(
            dividerColor: AppColors.primary,
            indicatorColor: AppColors.primary,
            unselectedLabelColor: AppColors.unselected,
            labelColor: AppColors.primary,
            isScrollable: true,
            tabs: campaignCategories
                .map((tab) => Tab(text: tab['name']))
                .toList(),
          ),
        ),
        body: BackgroundWrapper(
          child: TabBarView(
            children: campaignCategories.map((tab) {
              final id = tab['id'];
              return DonaitionCategoryViewPage(Category:widget. category);
            }).toList(),
          ),
        ),
      ),
    );
  }
}
// import 'package:charity_project/view/d.dart';
// import 'package:flutter/material.dart';

// class CampaignsPage extends StatelessWidget {
//   const CampaignsPage({super.key});

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       appBar: AppBar(title: const Text("Campaigns")),
//       body: ListView(
//         children: const [
//           CampaignCard(
//             title: "Water for All",
//             description: "Help us build wells in Africa.",
//             raisedAmount: 3000,
//             goalAmount: 5000,
//           ),
//           CampaignCard(
//             title: "School Bags",
//             description: "Provide school supplies for orphans.",
//             raisedAmount: 1200,
//             goalAmount: 2000,
//           ),
//         ],
//       ),
//     );
//   }
// }
