{let topic_list=fetch('content','list',hash( parent_node_id, $node.node_id,
                                             limit, 20,
                                             offset, $view_parameters.offset,
                                             sort_by, array( array( attribute, false(), 'forum_topic/sticky' ), array('modified_subnode',false()))))
     topic_count=fetch('content','list_count',hash(parent_node_id,$node.node_id))}

<div class="content-view-full">
    <div class="class-forum">

    <h1>{$node.name|wash}</h1>

    <div class="attribute-short">
    {attribute_view_gui attribute=$node.object.data_map.description}
    </div>


    {section show=$node.object.can_create}
        <form method="post" action={"content/action/"|ezurl}>
            <input class="button" type="submit" name="NewButton" value="New topic" />
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id.}" />
            <input class="button" type="submit" name="ActionAddToNotification" value="Keep me updated" />
            <input type="hidden" name="NodeID" value="{$node.node_id}" />
            <input type="hidden" name="ClassIdentifier" value="forum_topic" />
        </form>
    {section-else}
        <p>
        You need to be logged in to get access to the forums. You can do so <a href={"/user/login/"|ezurl}>here</a>
        </p>
    {/section}


    <div class="content-view-children">

        <table class="list forum" cellspacing="0">
        <tr>
            <th class="topic">
                {"Topic"|i18n("design/forum/layout")}
            </th>
            <th class="replies">
                {"Replies"|i18n("design/forum/layout")}
            </th>
            <th class="lastreply">
                {"Last reply"|i18n("design/forum/layout")}
            </th>
        </tr>

        {section var=topic loop=$topic_list sequence=array(bglight,bgdark)}
        <tr class="{$topic.sequence}">
            <td class="topic">
                <p>{section show=$topic.object.data_map.sticky.content}<img src={"sticky-16x16-icon.gif"|ezimage} height="16" width="16" align="middle" alt="" />{/section}
                <a href={$topic.url_alias|ezurl}>{$topic.object.name|wash}</a></p>
                <div class="attribute-byline">
                   <p class="author">{$topic.object.owner.name|wash}</p>
                   <p class="date">{$topic.object.published|l10n(shortdatetime)}</p>
                </div>
            </td>
            <td class="replies">
                <p>{fetch('content','tree_count',hash(parent_node_id,$topic.node_id))}</p>
            </td>
            <td class="lastreply">
            {let last_reply=fetch('content','list',hash( parent_node_id, $topic.node_id,
                                                         sort_by, array( array( 'published', false() ) ),
                                                         limit, 1 ) ) }
                {section var=reply loop=$last_reply show=$last_reply}
                <p><a href={concat($reply.parent.url_alias,'#msg',$reply.node_id)|ezurl}>{$reply.name|wash}</a></p>

                <div class="attribute-byline">
                   <p class="author">{$reply.object.owner.name|wash}</p>
                   <p class="date">{$reply.object.published|l10n(shortdatetime)}</p>
                </div>
                {/section}
           {/let}
           </td>
        </tr>
        {/section}
        </table>

    </div>

    </div>
</div>
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/content/view','/full/',$node.node_id)
         item_count=$child_count
         view_parameters=$view_parameters
         item_limit=20}

{/let}
