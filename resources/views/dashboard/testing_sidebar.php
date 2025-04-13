<div class="sidebar">

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            {{-- <li class="nav-item {{ Request::is('*dashboard*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ Request::is('*dashboard') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('*dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <p>Dashboard</p>
                </a>
            </li>


            {{-- --------------------- Home Page Start ------------------------ --}}
            <li class="nav-item {{ Request::is('*home*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Home
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('top-banner.index') }}"
                            class="nav-link {{ Request::is('*home/top_banner*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Top Banner</p>
                        </a>
                    </li>


                    {{-- <li class="nav-item {{ Request::is('*service*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*home/service*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Service
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('service-category.index') }}"
                                    class="nav-link {{ Request::is('*service/service_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Category </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('service-item.index') }}"
                                    class="nav-link {{ Request::is('*service/service_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Item</p>
                                </a>
                            </li>
                        </ul>
                    </li> --}}




                    <li class="nav-item">
                        <a href="{{ route('video.index') }}"
                            class="nav-link {{ Request::is('*home/video*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Video</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.index') }}"
                            class="nav-link {{ Request::is('*home/client*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Success Stories</p>
                        </a>
                    </li>


                    <li class="nav-item {{ Request::is('*service*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*service*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Service
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('service-category.index') }}"
                                    class="nav-link {{ Request::is('*service/service_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Departments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('service-item.index') }}"
                                    class="nav-link {{ Request::is('*service/service_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Categories</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('group-service-item.index') }}"
                                    class="nav-link {{ Request::is('*home/group/service/group_service_item') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service List</p>
                                </a>
                            </li>
                        </ul>
                    </li>



                    <li class="nav-item {{ Request::is('*marketing_house*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*home/marketing*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Marketing House
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('marketing-house-priority.index') }}"
                                    class="nav-link {{ Request::is('*marketing_house_priority*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>All Button Priority</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('marketing-house-category.index') }}"
                                    class="nav-link {{ Request::is('*marketing_house_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Marketing Category</p>
                                </a>
                            </li>

                            <!-- Marketing House Item-->
                            <li class="nav-item">
                                <a href="{{ route('marketing-house-item.index') }}"
                                    class="nav-link {{ Request::is('*marketing_house_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Marketing Item</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item {{ Request::is('*creative_house*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*home/creative_house*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Creative House
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('creative-house-priority.index') }}"
                                    class="nav-link {{ Request::is('*creative_house_priority*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>All Button Priority</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('creative-house-category.index') }}"
                                    class="nav-link {{ Request::is('*creative_house/creative_house_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creative Category</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('creative-house-item.index') }}"
                                    class="nav-link {{ Request::is('*creative_house/creative_house_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creative Item</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- <li class="nav-item {{ Request::is('*development_house*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::is('*home/development_house*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Development
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('development-house-category.index') }}"
                                    class="nav-link {{ Request::is('*home/development_house/development_house_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Development Category</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('development-house-item.index') }}"
                                    class="nav-link {{ Request::is('*home/development_house/development_house_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Development House</p>
                                </a>
                            </li>
                        </ul>
                    </li> --}}

                    <li class="nav-item {{ Request::is('*monthly_performance_showcase*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::is('*home/performance/monthly_performance_showcase*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Performance
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('monthly-performance-showcase-category.index') }}"
                                    class="nav-link {{ Request::is('*home/performance/monthly_performance_showcase_category*') ? 'active' : '' }}">
                                    {{-- <i class="far fa-circle nav-icon"></i> --}}
                                    <p>Performance Category</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('monthly-performance-showcase-subcategory.index') }}"
                                    class="nav-link {{ Request::is('*home/performance/monthly_performance_showcase_subcategory*') ? 'active' : '' }}">
                                    {{-- <i class="far fa-circle nav-icon"></i> --}}
                                    <p>Performance SubCategory</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('monthly-performance-showcase-item.index') }}"
                                    class="nav-link {{ Request::is('*home/performance/monthly_performance_showcase_item*') ? 'active' : '' }}">
                                    {{-- <i class="far fa-circle nav-icon"></i> --}}
                                    <p>Performance Item</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    {{-- <li class="nav-item {{ Request::is('*social_work*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*home/social_work*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Social Work
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('social-work-category.index') }}"
                                    class="nav-link {{ Request::is('*home/social_work/social_work_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Social Work Category</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('social-work-item.index') }}"
                                    class="nav-link {{ Request::is('*home/social_work/social_work_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Social Work</p>
                                </a>
                            </li>
                        </ul>
                    </li> --}}

                </ul>
            </li>
            {{-- --------------------- Home Page End ------------------------ --}}



            {{-- --------------------- Group Page  ------------------------ --}}
            {{-- <li class="nav-item {{ Request::is('*group*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Our Service
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">


                    <li class="nav-item {{ Request::is('*service*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*group/service*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Service
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('service-category.index') }}"
                                    class="nav-link {{ Request::is('*group/service/service_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Service Category </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('service-item.index') }}"
                                    class="nav-link {{ Request::is('*group/service/service_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Our Service </p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('group-top-banner.index') }}"
                            class="nav-link {{ Request::is('*group/service/group_service_top_banner*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p> Group Top Banner</p>
                        </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ route('brands.index') }}"
                          class="nav-link {{ Request::is('*group/brands*') ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Brands</p>
                      </a>
                  </li>


                    <li class="nav-item">
                        <a href="{{ route('group-service-category.index') }}"
                            class="nav-link {{ Request::is('*group/service/group_service_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Group Service Category</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('group-service-item.index') }}"
                            class="nav-link {{ Request::is('*group/service/group_service_item*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Group Service Item</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('group-single-service-img.index') }}"
                            class="nav-link {{ Request::is('*group/service/group_single_service_image*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Single Service Image</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('group-single-service-recent-work.index') }}"
                            class="nav-link {{ Request::is('*group/service/group_single_service_recent_work*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Recent Work</p>
                        </a>
                    </li>

                    <li class="nav-item {{ Request::is('*portfolio*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('*portfolio*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Portfolio
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('group-single-service-portfolio-category.index') }}"
                                    class="nav-link {{ Request::is('*group/service/portfolio/group_single_service_portfolio_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Portfolio Category</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('group-single-service-portfolio-item.index') }}"
                                    class="nav-link {{ Request::is('*group/service/portfolio/group_single_service_portfolio_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Portfolio Item</p>
                                </a>
                            </li>



                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('creator-platform.index') }}"
                            class="nav-link {{ Request::is('*group/creator_platform*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Content Service</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('success-stories.index') }}"
                            class="nav-link {{ Request::is('*group/success_stories*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Success Stories</p>
                        </a>
                    </li>

                </ul>
            </li> --}}
            {{-- --------------------- Group Page End  ------------------------ --}}



            {{-- --------------------- Marketing House Page  ------------------------ --}}
            <!--MarketingPage updated by satyam-->
            {{-- <li class="nav-item {{ Request::is('*marketing_house*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Marketing House
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a href="{{ route('marketing-house-category.index') }}"
                            class="nav-link {{ Request::is('*marketing_house_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Marketing Category</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('marketing-house-item.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_item*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Marketing Item</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('marketing-house-project.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_project*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Marketing Project</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('marketing-house-image.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_image*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Marketing Item Image</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('marketing-house-pre-launch-activity.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_pre_launch_activity*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Prelaunch Activities</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('marketing-house-other-activity-category.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_other_activity_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Other Activities Category</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('marketing-house-other-activity-item.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_other_activity_item*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Other Activities Item</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('marketing-house-content-created-category.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_content_created_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Content Category</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('marketing-house-content-created-item.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_content_created_item*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Content Item</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('carousels.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/carousels*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Content Item Carousels</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('community_program_category.index') }}"
                            class="nav-link {{ Request::is('*community_program_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Community Category</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('community_program_category_item.index') }}"
                            class="nav-link {{ Request::is('*community_program_item*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Community Items</p>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <!--MarketingPage updated by satyam-->
            {{-- --------------------- Marketing House Page End  ------------------------ --}}



            {{-- --------------------- Creative House Page  ------------------------ --}}
            {{-- <li class="nav-item {{ Request::is('*creative_house*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Creative House
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item {{ Request::is('*creative*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::is('*creative/creative_house*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Creative
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('creative-house-category.index') }}"
                                    class="nav-link {{ Request::is('*creative/creative_house/creative_house_category*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creative Category</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('creative-house-item.index') }}"
                                    class="nav-link {{ Request::is('*creative/creative_house/creative_house_item*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Creative House</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('creative-house-project.index') }}"
                            class="nav-link {{ Request::is('*creative_house/creative_house_project*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Creative Project</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('creative-house-approach.index') }}"
                            class="nav-link {{ Request::is('*creative_house/creative_house_approach*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Creative Approach</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('creative-house-final-output.index') }}"
                            class="nav-link {{ Request::is('*creative_house/creative_house_final_output*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Final Output</p>
                        </a>
                    </li>

                </ul>
            </li> --}}
            {{-- --------------------- Creative House Page End  ------------------------ --}}


            <li class="nav-item {{ Request::is('*template*') && !Request::is('*blog*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Common Element
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('brands.index') }}"
                            class="nav-link {{ Request::is('*template/brands*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Brands</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user-choice.index') }}"
                            class="nav-link {{ Request::is('*template/user_choice*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Hire Us </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('marketing-house-project.index') }}"
                            class="nav-link {{ Request::is('*marketing_house/marketing_house_project*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Marketing House Project</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('creative-house-project.index') }}"
                            class="nav-link {{ Request::is('*creative_house/creative_house_project*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Creative House Project</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('banner-title-template.index') }}"
                            class="nav-link {{ Request::is('*template/banner_title_template*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Common Banner</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('author-template.index') }}"
                            class="nav-link {{ Request::is('*template/author*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Author</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('book-call.index') }}"
                            class="nav-link {{ Request::is('*template/book_call*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Book A Call</p>
                        </a>
                    </li>

                </ul>
            </li>

            {{-- --------------------- Template Page   ------------------------ --}}
            {{-- <li class="nav-item {{ Request::is('*template*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Common Template
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a href="{{ route('banner-title-template.index') }}"
                            class="nav-link {{ Request::is('*template/banner_title_template*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Common Banner</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('author-template.index') }}"
                            class="nav-link {{ Request::is('*template/author*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Author</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('book-call.index') }}"
                            class="nav-link {{ Request::is('*template/book_call*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Book A Call</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('brands.index') }}"
                            class="nav-link {{ Request::is('*template/brands*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Brands</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user-choice.index') }}"
                            class="nav-link {{ Request::is('*template/user_choice*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Hire Us </p>
                        </a>
                    </li>
                </ul>
            </li> --}}
            {{-- --------------------- Template Page End  ------------------------ --}}




            {{-- <li class="nav-item {{ Request::is('*free_consultation*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Free Consultation
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview"> --}}
            <li class="nav-item">
                <a href="{{ route('free_consultation_category.index') }}"
                    class="nav-link {{ Request::is('*free_consultation_category*') ? 'active' : '' }}"
                    style="{{ Request::is('free_consultation_category') ? 'color: black !important; background-color: white !important;' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Consultation Enquiry</p>
                </a>
            </li>
            {{-- </ul>
      </li> --}}

            {{-- --------------------- Contact_Us Page   ------------------------ --}}
            <li class="nav-item">
                <a href="{{ route('contact_us.index') }}"
                    class="nav-link {{ Request::is('contact_us') ? 'active' : '' }}"
                    style="{{ Request::is('contact_us') ? 'color: black !important; background-color: white !important;' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Contact Us Enquiry</p>
                </a>
            </li>
            {{-- --------------------- Contact_Us Page End  ------------------------ --}}

            {{-- --------------------- Job List   ------------------------ --}}

            <li class="nav-item {{ Request::is('job_list*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('job_list*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Job
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('job-list.index') }}"
                            class="nav-link {{ Request::is('job_list') ? 'active' : '' }}"
                            style="{{ Request::is('job_list') ? 'color: black !important; background-color: white !important;' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Job List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('job-list.upload.index') }}"
                            class="nav-link {{ Request::is('job_list/upload') ? 'active' : '' }}"
                            style="{{ Request::is('job_list/upload') ? 'color: black !important; background-color: white !important;' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Upload Job</p>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="nav-item">
                <a href="{{ route('job-list.index') }}"
                    class="nav-link {{ Request::is('job_list') ? 'active' : '' }}"
                    style="{{ Request::is('job_list') ? 'color: black !important; background-color: white !important;' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Job List</p>
                </a>
            </li> --}}
            {{-- --------------------- Job List End  ------------------------ --}}

            <li class="nav-item">
                <a href="{{ route('gallery.index') }}"
                    class="nav-link {{ Request::is('gallery*') ? 'active' : '' }}"
                    style="{{ Request::is('gallery') ? 'color: black !important; background-color: white !important;' : '' }}">
                    <i class="nav-icon far fa-image"></i>
                    <p>Gallery</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('page.index') }}" class="nav-link {{ Request::is('page*') ? 'active' : '' }}"
                    style="{{ Request::is('page*') ? 'color: black !important; background-color: white !important;' : '' }}">
                    <i class="nav-icon far fa-image"></i>
                    <p>Website Pages</p>
                </a>
            </li>

            <li class="nav-item {{ Request::is('blog*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('blog*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Blogs
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('blog.index') }}"
                            class="nav-link {{ Request::is('blog*') && !Request::is('blog_sub_category*') && !Request::is('blog_items*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Blogs Category</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('blog_sub_category.index') }}"
                            class="nav-link {{ Request::is('blog_sub_category*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Blog SubCategory</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('blog_items.index') }}"
                            class="nav-link {{ Request::is('blog_items*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Blog Items</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- <li class="nav-item">
                          <a href="pages/widgets.html" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                              Widgets
                              <span class="right badge badge-danger">New</span>
                            </p>
                          </a>
                        </li> --}}
            {{-- <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-copy"></i>
                            <p>
                              Layout Options
                              <i class="fas fa-angle-left right"></i>
                              <span class="badge badge-info right">6</span>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/layout/top-nav.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Top Navigation</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/top-nav-sidebar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Top Navigation + Sidebar</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/boxed.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Boxed</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/fixed-sidebar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fixed Sidebar</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/fixed-sidebar-custom.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fixed Sidebar <small>+ Custom Area</small></p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/fixed-topnav.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fixed Navbar</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/fixed-footer.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Fixed Footer</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/layout/collapsed-sidebar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Collapsed Sidebar</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                              Charts
                              <i class="right fas fa-angle-left"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/charts/chartjs.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>ChartJS</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/charts/flot.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Flot</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/charts/inline.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inline</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/charts/uplot.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>uPlot</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tree"></i>
                            <p>
                              UI Elements
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/UI/general.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>General</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/icons.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Icons</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/buttons.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Buttons</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/sliders.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sliders</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/modals.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Modals & Alerts</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/navbar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Navbar & Tabs</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/timeline.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Timeline</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/UI/ribbons.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ribbons</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>
                              Forms
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/forms/general.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>General Elements</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/forms/advanced.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Advanced Elements</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/forms/editors.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Editors</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/forms/validation.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Validation</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-table"></i>
                            <p>
                              Tables
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/tables/simple.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Simple Tables</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/tables/data.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>DataTables</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/tables/jsgrid.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>jsGrid</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-header">EXAMPLES</li>
                        <li class="nav-item">
                          <a href="pages/calendar.html" class="nav-link">
                            <i class="nav-icon far fa-calendar-alt"></i>
                            <p>
                              Calendar
                              <span class="badge badge-info right">2</span>
                            </p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="pages/gallery.html" class="nav-link">
                            <i class="nav-icon far fa-image"></i>
                            <p>
                              Gallery
                            </p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="pages/kanban.html" class="nav-link">
                            <i class="nav-icon fas fa-columns"></i>
                            <p>
                              Kanban Board
                            </p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon far fa-envelope"></i>
                            <p>
                              Mailbox
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/mailbox/mailbox.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inbox</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/mailbox/compose.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Compose</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/mailbox/read-mail.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Read</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                              Pages
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/examples/invoice.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Invoice</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/profile.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Profile</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/e-commerce.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>E-commerce</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/projects.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Projects</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/project-add.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Project Add</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/project-edit.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Project Edit</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/project-detail.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Project Detail</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/contacts.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Contacts</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/faq.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>FAQ</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/contact-us.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Contact us</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon far fa-plus-square"></i>
                            <p>
                              Extras
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                  Login & Register v1
                                  <i class="fas fa-angle-left right"></i>
                                </p>
                              </a>
                              <ul class="nav nav-treeview">
                                <li class="nav-item">
                                  <a href="pages/examples/login.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Login v1</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/register.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Register v1</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/forgot-password.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Forgot Password v1</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/recover-password.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Recover Password v1</p>
                                  </a>
                                </li>
                              </ul>
                            </li>
                            <li class="nav-item">
                              <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                  Login & Register v2
                                  <i class="fas fa-angle-left right"></i>
                                </p>
                              </a>
                              <ul class="nav nav-treeview">
                                <li class="nav-item">
                                  <a href="pages/examples/login-v2.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Login v2</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/register-v2.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Register v2</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/forgot-password-v2.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Forgot Password v2</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="pages/examples/recover-password-v2.html" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Recover Password v2</p>
                                  </a>
                                </li>
                              </ul>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/lockscreen.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lockscreen</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/legacy-user-menu.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Legacy User Menu</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/language-menu.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Language Menu</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/404.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Error 404</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/500.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Error 500</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/pace.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pace</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/examples/blank.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Blank Page</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="starter.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Starter Page</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-search"></i>
                            <p>
                              Search
                              <i class="fas fa-angle-left right"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="pages/search/simple.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Simple Search</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="pages/search/enhanced.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Enhanced</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-header">MISCELLANEOUS</li>
                        <li class="nav-item">
                          <a href="iframe.html" class="nav-link">
                            <i class="nav-icon fas fa-ellipsis-h"></i>
                            <p>Tabbed IFrame Plugin</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="https://adminlte.io/docs/3.1/" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Documentation</p>
                          </a>
                        </li>
                        <li class="nav-header">MULTI LEVEL EXAMPLE</li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="fas fa-circle nav-icon"></i>
                            <p>Level 1</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-circle"></i>
                            <p>
                              Level 1
                              <i class="right fas fa-angle-left"></i>
                            </p>
                          </a>
                          <ul class="nav nav-treeview">
                            <li class="nav-item">
                              <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                  Level 2
                                  <i class="right fas fa-angle-left"></i>
                                </p>
                              </a>
                              <ul class="nav nav-treeview">
                                <li class="nav-item">
                                  <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Level 3</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Level 3</p>
                                  </a>
                                </li>
                                <li class="nav-item">
                                  <a href="#" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Level 3</p>
                                  </a>
                                </li>
                              </ul>
                            </li>
                            <li class="nav-item">
                              <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="fas fa-circle nav-icon"></i>
                            <p>Level 1</p>
                          </a>
                        </li>
                        <li class="nav-header">LABELS</li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon far fa-circle text-danger"></i>
                            <p class="text">Important</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon far fa-circle text-warning"></i>
                            <p>Warning</p>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="#" class="nav-link">
                            <i class="nav-icon far fa-circle text-info"></i>
                            <p>Informational</p>
                          </a>
                        </li> --}}
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
